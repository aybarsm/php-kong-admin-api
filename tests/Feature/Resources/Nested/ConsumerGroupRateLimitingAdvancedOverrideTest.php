<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Enums\RateLimitWindowType;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnauthorizedException;
use Aybarsm\Kong\AdminApi\Models\RateLimitingAdvancedOverride;
use Aybarsm\Kong\AdminApi\Models\RateLimitingAdvancedOverrideInput;
use Aybarsm\Kong\AdminApi\Resources\ConsumerGroups;
use Aybarsm\Kong\AdminApi\Resources\Nested\ConsumerGroupRateLimitingAdvancedOverride;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(ConsumerGroupRateLimitingAdvancedOverride::class, ConsumerGroups::class, RateLimitingAdvancedOverrideInput::class);

it('sets the override with PUT, sending the spec dotted keys (spec-notes Q10)', function (): void {
    $kong = MockKong::queue(MockKong::json(201, Fixture::get('rate_limiting_advanced_override')));

    $override = $kong->client->consumerGroups()->rateLimitingAdvancedOverride('gold tier')->upsert(new RateLimitingAdvancedOverrideInput(
        configLimit: '10',
        configWindowSize: '60',
        configRetryAfterJitterMax: '0',
        configWindowType: RateLimitWindowType::Fixed,
    ));

    expect($kong->lastRequest()->getMethod())->toBe('PUT')
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/consumer_groups/gold%20tier/overrides/plugins/rate-limiting-advanced')
        ->and($kong->lastJsonBody())->toBe([
            'config.limit' => '10',
            'config.window_size' => '60',
            'config.retry_after_jitter_max' => '0',
            'config.window_type' => 'fixed',
        ])
        ->and($override)->toEqual(RateLimitingAdvancedOverride::fromArray(Fixture::get('rate_limiting_advanced_override')));
});

it('accepts an array body in any shape', function (): void {
    $kong = MockKong::queue(MockKong::json(201, Fixture::get('rate_limiting_advanced_override')));

    $kong->client->consumerGroups()->rateLimitingAdvancedOverride('g')->upsert(['config' => ['limit' => [10], 'window_size' => [60]]]);

    expect($kong->lastJsonBody())->toBe(['config' => ['limit' => [10], 'window_size' => [60]]]);
});

it('deletes the override', function (): void {
    $kong = MockKong::queue(MockKong::raw(204));

    $kong->client->inWorkspace('team-a')->consumerGroups()->rateLimitingAdvancedOverride('g')->delete();

    expect($kong->lastRequest()->getMethod())->toBe('DELETE')
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/team-a/consumer_groups/g/overrides/plugins/rate-limiting-advanced')
        ->and((string) $kong->lastRequest()->getBody())->toBe('');
});

it('rejects an empty group ID', function (): void {
    expect(fn (): ConsumerGroupRateLimitingAdvancedOverride => MockKong::queue()->client->consumerGroups()->rateLimitingAdvancedOverride(''))
        ->toThrow(InvalidArgumentException::class, 'Consumer Group ID must not be empty.');
});

it('maps 401', function (): void {
    $kong = MockKong::queue(MockKong::json(401, ['message' => 'Unauthorized', 'status' => 401]));

    expect(fn (): RateLimitingAdvancedOverride => $kong->client->consumerGroups()->rateLimitingAdvancedOverride('g')->upsert([]))
        ->toThrow(UnauthorizedException::class, 'Unauthorized');
});
