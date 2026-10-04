<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Config\ClientConfig;
use Aybarsm\Kong\AdminApi\Enums\Protocol;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Exceptions\KongExceptionInterface;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\Exceptions\TransportException;
use Aybarsm\Kong\AdminApi\Exceptions\ValidationException;
use Aybarsm\Kong\AdminApi\KongClient;
use Aybarsm\Kong\AdminApi\Models\ConsumerInput;
use Aybarsm\Kong\AdminApi\Models\KeyAuthInput;
use Aybarsm\Kong\AdminApi\Models\PluginInput;
use Aybarsm\Kong\AdminApi\Models\RouteExpression;
use Aybarsm\Kong\AdminApi\Models\RouteJson;
use Aybarsm\Kong\AdminApi\Models\RouteJsonInput;
use Aybarsm\Kong\AdminApi\Models\Service;
use Aybarsm\Kong\AdminApi\Models\ServiceInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\TagFilter;
use Aybarsm\Kong\AdminApi\Plugins\PluginRegistry;
use Aybarsm\Kong\AdminApi\Plugins\TrafficControl\RateLimiting\RateLimitingConfig;
use Aybarsm\Kong\AdminApi\Plugins\TrafficControl\RateLimiting\RateLimitingConfigInput;
use Aybarsm\Kong\AdminApi\Plugins\TrafficControl\RateLimiting\RateLimitingInput;
use Aybarsm\Kong\AdminApi\Plugins\TrafficControl\RateLimiting\RateLimitingPolicy;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Request;

/*
 * Every code example in README.md is mirrored here, against MockHandler instead of a live Kong. If you
 * change an example in the README, change it here too (and vice versa). The only difference: `$kong` is
 * the mock client instead of `new KongClient(new ClientConfig('http://localhost:8001/'))`, whose
 * construction the configuration example covers.
 */

it('README: quick start', function (): void {
    $mock = MockKong::queue(
        MockKong::json(201, Fixture::get('service')),
        MockKong::json(201, Fixture::get('route_json')),
        MockKong::json(201, Fixture::get('plugin')),
    );
    $kong = $mock->client;

    // --- README ---
    $service = $kong->services()->create(new ServiceInput(
        name: 'billing',
        url: 'http://billing.internal:8080',
    ));

    $route = $kong->services()->routes('billing')->create(new RouteJsonInput(
        name: 'billing-api',
        paths: ['/billing'],
        protocols: [Protocol::Https],
    ));

    $kong->services()->plugins('billing')->create(new PluginInput(
        name: 'rate-limiting',
        config: ['minute' => 100, 'policy' => 'local'],
    ));
    // --- /README ---

    expect($service)->toBeInstanceOf(Service::class)
        ->and($route)->toBeInstanceOf(RouteJson::class)
        ->and($mock->requestAt(0)->getUri()->getPath())->toBe('/services')
        ->and($mock->requestAt(1)->getUri()->getPath())->toBe('/services/billing/routes')
        ->and($mock->requestAt(2)->getUri()->getPath())->toBe('/services/billing/plugins')
        ->and($mock->lastJsonBody())->toBe(['name' => 'rate-limiting', 'config' => ['minute' => 100, 'policy' => 'local']]);
});

it('README: configuration', function (): void {
    putenv('KONG_ADMIN_TOKEN=s3cr3t');

    // --- README ---
    $token = getenv('KONG_ADMIN_TOKEN');

    $kong = new KongClient(new ClientConfig(
        baseUri: 'https://kong-admin.internal:8444/',
        adminToken: $token === false ? null : $token,
        workspace: null,
        timeout: 10.0,
        connectTimeout: 2.0,
        headers: ['X-Request-Source' => 'deploy-bot'],
    ));
    // --- /README ---

    expect($kong->config()->baseUri)->toBe('https://kong-admin.internal:8444/')
        ->and($kong->config()->adminToken)->toBe('s3cr3t');
    putenv('KONG_ADMIN_TOKEN');
});

it('README: bring your own PSR-18 client', function (): void {
    $handler = HandlerStack::create(new MockHandler([MockKong::json(200, Fixture::get('service'))]));

    // --- README ---
    $http = new Client([
        'handler' => $handler,   // e.g. HandlerStack::create() with your own middleware
        'timeout' => 5,
        'verify' => '/etc/ssl/kong-ca.pem',
    ]);

    $kong = new KongClient(
        new ClientConfig('https://kong-admin.internal:8444/'),
        $http,              // any Psr\Http\Client\ClientInterface
        new HttpFactory(),  // any PSR-17 request + stream factory
    );
    // --- /README ---

    expect($kong->services()->get('billing')->host)->toBe('example.internal');
});

it('README: workspaces', function (): void {
    $mock = MockKong::queue(MockKong::json(200, ['data' => []]), MockKong::json(200, ['data' => []]));
    $kong = $mock->client;

    // --- README ---
    $payments = $kong->inWorkspace('team-payments');
    $payments->services()->list();      // GET /team-payments/services
    $payments->workspaces()->list();    // GET /workspaces (global-only path, never prefixed)
    // --- /README ---

    expect($mock->requestAt(0)->getUri()->getPath())->toBe('/team-payments/services')
        ->and($mock->requestAt(1)->getUri()->getPath())->toBe('/workspaces');
});

it('README: nested resources follow the spec paths', function (): void {
    $responses = array_fill(0, 9, MockKong::json(200, ['data' => []]));
    $mock = MockKong::queue(...$responses);
    $kong = $mock->client;
    $consumerId = 'c1';
    $upstreamId = 'u1';
    $certificateId = 'cert1';
    $partialId = 'p1';

    // --- README ---
    $resources = [
        $kong->services()->routes('billing'),              // /services/{ServiceIdOrName}/routes
        $kong->services()->plugins('billing'),             // /services/{ServiceIdOrName}/plugins
        $kong->routes()->plugins('billing-api'),           // /routes/{RouteIdOrName}/plugins
        $kong->consumers()->plugins($consumerId),          // /consumers/{ConsumerIdForNestedEntities}/plugins
        $kong->consumerGroups()->consumers('gold'),        // /consumer_groups/{ConsumerGroupId}/consumers
        $kong->upstreams()->targets($upstreamId),          // /upstreams/{UpstreamIdForTarget}/targets
        $kong->certificates()->snis($certificateId),       // /certificates/{CertificateId}/snis
        $kong->keySets()->keys('jwks'),                    // /key-sets/{KeySetIdOrName}/keys
        $kong->partials()->links($partialId),              // /partials/{PartialId}/links
    ];
    // --- /README ---

    foreach ($resources as $resource) {
        $resource->list();
    }
    $paths = array_map(static fn (int $i): string => $mock->requestAt($i)->getUri()->getPath(), range(0, 8));

    expect($paths)->toBe([
        '/services/billing/routes',
        '/services/billing/plugins',
        '/routes/billing-api/plugins',
        '/consumers/c1/plugins',
        '/consumer_groups/gold/consumers',
        '/upstreams/u1/targets',
        '/certificates/cert1/snis',
        '/key-sets/jwks/keys',
        '/partials/p1/links',
    ]);
});

it('README: pagination and tag filters', function (): void {
    $mock = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('service')], 'offset' => 'b2Zm']),
        MockKong::json(200, ['data' => []]),
        MockKong::json(200, ['data' => [Fixture::get('service')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('service')]]),
    );
    $kong = $mock->client;
    $seen = [];

    // --- README ---
    $options = new ListOptions(size: 100, tags: TagFilter::allOf('production', 'billing'));

    $page = $kong->services()->list($options);
    foreach ($page->data as $service) {
        $seen[] = $service->name;
    }
    $more = $page->hasMore();                               // true when Kong returned an `offset`
    $next = $kong->services()->nextPage($page, $options);   // same filters; null after the last page

    foreach ($kong->services()->all(new ListOptions(tags: TagFilter::anyOf('team-a', 'team-b'))) as $service) {
        $seen[] = $service->name;   // fetched page by page, lazily
    }
    // --- /README ---

    expect($seen)->toHaveCount(3)
        ->and($more)->toBeTrue()
        ->and($next?->data)->toBe([])
        ->and($mock->queryAt(1))->toBe(['size' => '100', 'offset' => 'b2Zm', 'tags' => 'production,billing'])
        ->and($page->offset)->toBe('b2Zm')
        ->and($mock->queryAt(0))->toBe(['size' => '100', 'tags' => 'production,billing'])
        ->and($mock->queryAt(3))->toBe(['offset' => 'p2', 'tags' => 'team-a/team-b']);
});

it('README: input DTOs and arrays', function (): void {
    $mock = MockKong::queue(MockKong::json(200, Fixture::get('service')), MockKong::json(200, Fixture::get('service')));
    $kong = $mock->client;

    // --- README ---
    // Typed input: null fields are not sent.
    $kong->services()->update('billing', new ServiceInput(retries: 3));          // {"retries":3}

    // Arrays are sent as given, including explicit nulls.
    $kong->services()->update('billing', ['ca_certificates' => null]);          // {"ca_certificates":null}
    // --- /README ---

    expect((string) $mock->requestAt(0)->getBody())->toBe('{"retries":3}')
        ->and((string) $mock->requestAt(1)->getBody())->toBe('{"ca_certificates":null}');
});

it('README: consumers and credentials', function (): void {
    $mock = MockKong::queue(MockKong::json(201, Fixture::get('consumer')), MockKong::json(201, Fixture::get('key_auth')));
    $kong = $mock->client;

    // --- README ---
    $consumer = $kong->consumers()->create(new ConsumerInput(username: 'alice', customId: 'crm-42'));
    $kong->consumers()->keyAuths((string) $consumer->id)->create(new KeyAuthInput(ttl: 3600));
    // --- /README ---

    expect($mock->requestAt(1)->getUri()->getPath())->toBe('/consumers/' . $consumer->id . '/key-auth');
});

it('README: route variants', function (): void {
    $mock = MockKong::queue(MockKong::json(200, Fixture::get('route_expression')));
    $kong = $mock->client;
    $match = '';

    // --- README ---
    $route = $kong->routes()->get('billing-api');

    $match = match (true) {
        $route instanceof RouteExpression => (string) $route->expression,
        $route instanceof RouteJson => implode(', ', $route->paths ?? []),
        default => '',
    };
    // --- /README ---

    expect($match)->toContain('http.path');
});

it('README: error handling', function (): void {
    $mock = MockKong::queue(
        MockKong::raw(404),
        MockKong::json(400, ['code' => 2, 'name' => 'schema violation', 'message' => 'schema violation (host: required field missing)', 'fields' => ['host' => 'required field missing']]),
        new ConnectException('Connection refused', new Request('GET', 'http://kong.test/services')),
    );
    $kong = $mock->client;
    $log = [];

    // --- README ---
    try {
        $kong->services()->get('missing');
    } catch (NotFoundException $e) {
        $log[] = $e->statusCode;                    // 404
    }

    try {
        $kong->services()->create(['name' => 'no-host']);
    } catch (ValidationException $e) {
        $log[] = $e->kongMessage;                   // "schema violation (host: required field missing)"
        $log[] = $e->errorName;                     // "schema violation" (Kong error code 2)
        $log[] = $e->errorFields;                   // ['host' => 'required field missing']
    }

    try {
        $kong->services()->list();
    } catch (TransportException $e) {
        $log[] = $e->getPrevious()?->getMessage();  // the PSR-18 client's exception
    } catch (KongApiException $e) {
        // any other Admin API failure
    }
    // --- /README ---

    expect($log)->toBe([
        404,
        'schema violation (host: required field missing)',
        'schema violation',
        ['host' => 'required field missing'],
        'Connection refused',
    ])->and(new ValidationException('x'))->toBeInstanceOf(KongExceptionInterface::class);
});

it('README: operational endpoints', function (): void {
    $mock = MockKong::queue(
        MockKong::json(200, Fixture::get('kong_info')),
        MockKong::raw(204),
        MockKong::json(200, ['message' => 'schema validation successful']),
    );
    $kong = $mock->client;

    // --- README ---
    $version = $kong->information()->info()->version;
    $hasVaults = $kong->information()->endpointExists('vaults');
    $check = $kong->schemas()->validate('services', ['host' => 'billing.internal']);
    // --- /README ---

    expect($version)->toBe(Fixture::get('kong_info')['version'])
        ->and($hasVaults)->toBeTrue()
        ->and($check->message)->toBe('schema validation successful');
});

it('README: typed plugins', function (): void {
    $rateLimiting = [...Fixture::get('plugin'), 'name' => 'rate-limiting', 'config' => Fixture::get('Plugins/TrafficControl/rate-limiting')];
    $mock = MockKong::queue(
        MockKong::json(201, $rateLimiting),
        MockKong::json(200, ['data' => [$rateLimiting, [...Fixture::get('plugin'), 'name' => 'my-custom-plugin']]]),
    );
    $kong = $mock->client;

    // --- README ---
    $plugin = $kong->services()->plugins('billing')->create(new RateLimitingInput(
        config: new RateLimitingConfigInput(minute: 100, policy: RateLimitingPolicy::Local),
        tags: ['edge'],
    ));

    $limits = RateLimitingConfig::fromPlugin($plugin);   // typed config of a rate-limiting plugin
    $minute = $limits->minute;

    // Typed configs of any listed plugins; null for a plugin without a doc.
    $configs = array_map(PluginRegistry::config(...), $kong->plugins()->list()->data);
    // --- /README ---

    expect($mock->requestAt(0)->getUri()->getPath())->toBe('/services/billing/plugins')
        ->and($mock->queryAt(1))->toBe([])
        ->and($minute)->toBe(1.5)
        ->and($configs[0])->toBeInstanceOf(RateLimitingConfig::class)
        ->and($configs[1])->toBeNull();
    $body = json_decode((string) $mock->requestAt(0)->getBody(), true);
    expect($body)->toEqual(['name' => 'rate-limiting', 'config' => ['minute' => 100, 'policy' => 'local'], 'tags' => ['edge']]);
});

it('mirrors every php code block of README.md in this file', function (): void {
    $root = dirname(__DIR__, 2);
    $readme = (string) file_get_contents($root . '/README.md');
    $self = (string) file_get_contents(__FILE__);

    preg_match_all('/```php\n(.*?)```/s', $readme, $blocks);
    preg_match_all('/\/\/ --- README ---\n(.*?)\n\s*\/\/ --- \/README ---/s', $self, $sections);

    $normalise = static fn (string $code): string => implode("\n", array_filter(
        array_map(static fn (string $line): string => trim($line), explode("\n", $code)),
        static fn (string $line): bool => $line !== ''
            && !str_starts_with($line, 'use ')
            && $line !== "\$kong = new KongClient(new ClientConfig('http://localhost:8001/'));",
    ));
    $mirrored = array_map($normalise, $sections[1]);

    $missing = [];
    foreach ($blocks[1] as $block) {
        $code = $normalise($block);
        $found = array_filter($mirrored, static fn (string $section): bool => str_contains($section, $code));
        if ($found === []) {
            $missing[] = strtok($code, "\n");
        }
    }

    expect($blocks[1])->not->toBeEmpty()
        ->and($missing)->toBe([]);
});
