<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Enums\PartialType;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\ValidationException;
use Aybarsm\Kong\AdminApi\Models\PartialTypeSchema;
use Aybarsm\Kong\AdminApi\Models\PluginConfigSchema;
use Aybarsm\Kong\AdminApi\Models\SchemaValidation;
use Aybarsm\Kong\AdminApi\Resources\Schemas;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(Schemas::class);

it('validates an entity against its schema', function (): void {
    $kong = MockKong::queue(MockKong::json(200, ['message' => 'schema validation successful']));

    $result = $kong->client->inWorkspace('team-a')->schemas()->validate('services', ['host' => 'example.internal']);

    expect($result)->toEqual(new SchemaValidation('schema validation successful'))
        ->and($kong->lastRequest()->getMethod())->toBe('POST')
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/schemas/services/validate')
        ->and($kong->lastJsonBody())->toBe(['host' => 'example.internal']);
});

it('fetches Partial and plugin schemas', function (PartialType|string $type, string $path): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('partial_type_schema')));

    $schema = $kong->client->schemas()->partial($type);

    expect($schema)->toEqual(PartialTypeSchema::fromArray(Fixture::get('partial_type_schema')))
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path);
})->with([
    'enum' => [PartialType::RedisEe, '/schemas/partials/redis-ee'],
    'string' => ['vectordb', '/schemas/partials/vectordb'],
]);

it('fetches a plugin configuration schema', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('plugin_config_schema')));

    expect($kong->client->schemas()->plugin('rate-limiting'))->toEqual(PluginConfigSchema::fromArray(Fixture::get('plugin_config_schema')))
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/schemas/plugins/rate-limiting');
});

it('maps a failed validation and rejects empty names', function (): void {
    $kong = MockKong::queue(MockKong::json(400, ['message' => 'schema violation (host: required field missing)']));

    expect(fn (): SchemaValidation => $kong->client->schemas()->validate('services', []))->toThrow(ValidationException::class, 'required field missing')
        ->and(fn (): PluginConfigSchema => $kong->client->schemas()->plugin(''))->toThrow(InvalidArgumentException::class)
        ->and(fn (): PartialTypeSchema => $kong->client->schemas()->partial(''))->toThrow(InvalidArgumentException::class);
});
