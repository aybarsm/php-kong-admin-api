<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginRegistry;
use Aybarsm\Kong\AdminApi\Plugins\TrafficControl\RateLimiting\Policy;
use Aybarsm\Kong\AdminApi\Plugins\TrafficControl\RateLimiting\RateLimitingConfig;
use Aybarsm\Kong\AdminApi\Plugins\TrafficControl\RateLimiting\RateLimitingConfigInput;
use Aybarsm\Kong\AdminApi\Plugins\TrafficControl\RateLimiting\RateLimitingInput;
use Aybarsm\Kong\AdminApi\Resources\Nested\ConsumerGroupPlugins;
use Aybarsm\Kong\AdminApi\Resources\Nested\ConsumerPlugins;
use Aybarsm\Kong\AdminApi\Resources\Nested\RoutePlugins;
use Aybarsm\Kong\AdminApi\Resources\Nested\ServicePlugins;
use Aybarsm\Kong\AdminApi\Resources\Plugins;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

/*
 * The plugin resources accept a generated per-plugin body (TypedPluginInput) on every write, and send its
 * `name` and typed `config`. Responses stay the generic Plugin; the typed config is read with fromPlugin().
 */

covers(Plugins::class, ServicePlugins::class, RoutePlugins::class, ConsumerPlugins::class, ConsumerGroupPlugins::class, PluginRegistry::class);

function typedRateLimiting(): RateLimitingInput
{
    return new RateLimitingInput(
        config: new RateLimitingConfigInput(minute: 20, policy: Policy::Local),
        enabled: true,
    );
}

/**
 * @return array<string, mixed>
 */
function rateLimitingResponse(): array
{
    return [...Fixture::get('plugin'), 'name' => 'rate-limiting', 'config' => Fixture::get('Plugins/TrafficControl/rate-limiting')];
}

it('sends a typed plugin body on every write of every plugin resource', function (string $resource, string $path): void {
    $kong = MockKong::queue(...array_fill(0, 3, MockKong::json(200, rateLimitingResponse())));
    $plugins = match ($resource) {
        'plugins' => $kong->client->plugins(),
        'service' => $kong->client->services()->plugins('billing'),
        'route' => $kong->client->routes()->plugins('billing-api'),
        'consumer' => $kong->client->consumers()->plugins('alice'),
        'consumer group' => $kong->client->consumerGroups()->plugins('gold'),
        default => throw new LogicException($resource),
    };
    $body = ['name' => 'rate-limiting', 'config' => ['minute' => 20, 'policy' => 'local'], 'enabled' => true];

    $created = $plugins->create(typedRateLimiting());
    expect($kong->lastRequest()->getMethod())->toBe('POST')
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and($kong->lastJsonBody())->toEqual($body);

    $plugins->update('p1', typedRateLimiting());
    expect($kong->lastRequest()->getMethod())->toBe('PATCH')
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path . '/p1')
        ->and($kong->lastJsonBody())->toEqual($body);

    $plugins->upsert('p1', typedRateLimiting());
    expect($kong->lastRequest()->getMethod())->toBe('PUT')
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path . '/p1')
        ->and($kong->lastJsonBody())->toEqual($body)
        ->and($created)->toBeInstanceOf(Plugin::class)
        ->and(RateLimitingConfig::fromPlugin($created)->policy)->toBe(Policy::Cluster);
})->with([
    'plugins()' => ['plugins', '/plugins'],
    'services()->plugins()' => ['service', '/services/billing/plugins'],
    'routes()->plugins()' => ['route', '/routes/billing-api/plugins'],
    'consumers()->plugins()' => ['consumer', '/consumers/alice/plugins'],
    'consumerGroups()->plugins()' => ['consumer group', '/consumer_groups/gold/plugins'],
]);

it('maps a listed plugin to its typed config, and leaves undocumented plugins untyped', function (): void {
    $kong = MockKong::queue(MockKong::json(200, ['data' => [
        rateLimitingResponse(),
        [...Fixture::get('plugin'), 'name' => 'rate-limiting-advanced', 'config' => ['limit' => [5]]],
    ]]));

    $configs = array_map(PluginRegistry::config(...), $kong->client->inWorkspace('team-a')->plugins()->list()->data);

    expect($configs[0])->toBeInstanceOf(RateLimitingConfig::class)
        ->and($configs[1])->toBeNull()
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/team-a/plugins');
});
