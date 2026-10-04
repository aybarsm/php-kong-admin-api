<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Models\DeclarativeConfig;
use Aybarsm\Kong\AdminApi\Resources\DeclarativeConfig as DeclarativeConfigResource;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(DeclarativeConfigResource::class, Aybarsm\Kong\AdminApi\Internal\Transport::class);

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

it('applies a YAML declarative configuration as application/yaml (spec-notes Q20)', function (): void {
    $kong = MockKong::queue(MockKong::json(201, ['services' => []]));
    $yaml = "_format_version: '3.0'\nservices: []\n";

    $result = $kong->client->declarativeConfig()->applyYaml($yaml);

    expect($kong->lastRequest()->getMethod())->toBe('POST')
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/config')
        ->and($kong->lastRequest()->getHeaderLine('Content-Type'))->toBe('application/yaml')
        ->and($kong->lastRequest()->getHeaderLine('Accept'))->toBe('application/json')
        ->and((string) $kong->lastRequest()->getBody())->toBe($yaml)
        ->and($result)->toBe(['services' => []]);
});

it('maps a YAML validation error', function (): void {
    $kong = MockKong::queue(MockKong::json(400, ['code' => 14, 'name' => 'invalid declarative configuration', 'message' => 'declarative config is invalid', 'fields' => ['services' => 'expected an array']]));

    try {
        $kong->client->declarativeConfig()->applyYaml('services: x');
        throw new RuntimeException('Expected an exception');
    } catch (Aybarsm\Kong\AdminApi\Exceptions\ValidationException $e) {
        expect($e->errorCode)->toBe(14)
            ->and($e->errorFields)->toBe(['services' => 'expected an array']);
    }
});
