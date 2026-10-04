<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Models\DeclarativeConfig;
use Aybarsm\Kong\AdminApi\Resources\DeclarativeConfig as DeclarativeConfigResource;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(DeclarativeConfigResource::class);

it('gets the declarative configuration', function (): void {
    $kong = MockKong::queue(MockKong::json(200, ['config' => "_format_version: '3.0'"]));

    $config = $kong->client->inWorkspace('team-a')->declarativeConfig()->get();

    expect($config)->toEqual(new DeclarativeConfig("_format_version: '3.0'"))
        ->and($kong->lastRequest()->getMethod())->toBe('GET')
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/config');
});

it('applies a declarative configuration as JSON and returns the free-form response', function (): void {
    $kong = MockKong::queue(MockKong::json(201, ['services' => []]));

    $result = $kong->client->declarativeConfig()->apply(['_format_version' => '3.0', 'services' => []]);

    expect($kong->lastRequest()->getMethod())->toBe('POST')
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/config')
        ->and($kong->lastRequest()->getHeaderLine('Content-Type'))->toBe('application/json')
        ->and($kong->lastJsonBody())->toBe(['_format_version' => '3.0', 'services' => []])
        ->and($result)->toBe(['services' => []]);
});
