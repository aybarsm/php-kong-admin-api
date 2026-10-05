<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Config\ClientConfig;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\KongClient;
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;
use Aybarsm\Kong\AdminApi\Resources\Routes;
use Aybarsm\Kong\AdminApi\Resources\Services;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

covers(KongClient::class);

it('builds a default Guzzle client and exposes its configuration', function (): void {
    $config = new ClientConfig('http://kong.test/', timeout: 5.0, connectTimeout: 1.0);
    $client = new KongClient($config);

    expect($client->config())->toBe($config)
        ->and($client->services())->toBeInstanceOf(Services::class)
        ->and($client->routes())->toBeInstanceOf(Routes::class);
});

/**
 * The default Guzzle client's request options, read back from the client KongClient built.
 *
 * @return array<string, mixed>
 */
function defaultClientOptions(KongClient $client): array
{
    $http = (new ReflectionProperty(KongClient::class, 'httpClient'))->getValue($client);
    if (!$http instanceof Client) {
        throw new LogicException('Expected the default Guzzle client');
    }
    // Guzzle's own getConfig() is deprecated; it returns this property.
    $options = (new ReflectionProperty(Client::class, 'config'))->getValue($http);
    if (!is_array($options)) {
        throw new LogicException('Expected Guzzle options');
    }

    return array_intersect_key($options, array_flip([
        RequestOptions::HTTP_ERRORS, RequestOptions::VERIFY, RequestOptions::TIMEOUT,
        RequestOptions::CONNECT_TIMEOUT, RequestOptions::CERT, RequestOptions::SSL_KEY,
    ]));
}

it('passes timeouts and TLS settings to the default Guzzle client', function (ClientConfig $config, array $expected): void {
    $options = defaultClientOptions(new KongClient($config));
    ksort($options);
    ksort($expected);

    expect($options)->toBe($expected);
})->with([
    'defaults' => [new ClientConfig(), ['http_errors' => false, 'verify' => true]],
    'timeouts' => [new ClientConfig(timeout: 5.0, connectTimeout: 1.0), ['http_errors' => false, 'verify' => true, 'timeout' => 5.0, 'connect_timeout' => 1.0]],
    'verification off' => [new ClientConfig(verify: false), ['http_errors' => false, 'verify' => false]],
    'CA bundle' => [new ClientConfig(verify: __FILE__), ['http_errors' => false, 'verify' => __FILE__]],
    'certificate holding its key' => [new ClientConfig(clientCert: __FILE__), ['http_errors' => false, 'verify' => true, 'cert' => __FILE__]],
    'certificate and key' => [new ClientConfig(clientCert: __FILE__, clientKey: __DIR__ . '/KongSpecTest.php'), ['http_errors' => false, 'verify' => true, 'cert' => __FILE__, 'ssl_key' => __DIR__ . '/KongSpecTest.php']],
    'encrypted key' => [
        new ClientConfig(clientCert: __FILE__, clientKey: __DIR__ . '/KongSpecTest.php', clientKeyPassphrase: 'pw'),
        ['http_errors' => false, 'verify' => true, 'cert' => [__FILE__, 'pw'], 'ssl_key' => [__DIR__ . '/KongSpecTest.php', 'pw']],
    ],
]);

it('keeps the key passphrase and admin token out of dumps of the client and its resources', function (): void {
    $client = new KongClient(new ClientConfig(adminToken: 'token-secret', clientCert: __FILE__, clientKeyPassphrase: 'passphrase-secret'));

    expect(print_r($client, true))->not->toContain('secret')->toContain(Client::class)
        ->and(print_r($client->inWorkspace('team-a')->services(), true))->not->toContain('secret')->toContain('team-a')
        ->and($client->__debugInfo())->toBe(['config' => $client->config(), 'httpClient' => Client::class]);
});

it('works with no arguments using the spec defaults', function (): void {
    expect((new KongClient())->config()->baseUri)->toBe('http://localhost:8001/');
});

it('is immutable when switching workspaces', function (): void {
    $client = new KongClient(new ClientConfig('http://kong.test/'));
    $scoped = $client->inWorkspace('team-a');

    expect($scoped)->not->toBe($client)
        ->and($scoped->config()->workspace)->toBe('team-a')
        ->and($client->config()->workspace)->toBeNull()
        ->and($scoped->withoutWorkspace()->config()->workspace)->toBeNull();
});

it('rejects an empty workspace name', function (): void {
    expect(fn (): KongClient => (new KongClient())->inWorkspace(''))->toThrow(InvalidArgumentException::class);
});

it('returns a fresh resource from every accessor, keeping the workspace', function (): void {
    $client = new KongClient(new ClientConfig('http://kong.test/'));
    $checked = 0;

    foreach ((new ReflectionClass(KongClient::class))->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
        $return = $method->getReturnType();
        if (!$return instanceof ReflectionNamedType || !is_subclass_of($return->getName(), AbstractResource::class) || $method->getNumberOfParameters() > 0) {
            continue;
        }

        $resource = $method->invoke($client);
        expect($resource)->toBeInstanceOf($return->getName())
            ->and($method->invoke($client))->not->toBe($resource);
        $checked++;
    }

    expect($checked)->toBeGreaterThanOrEqual(13);
});
