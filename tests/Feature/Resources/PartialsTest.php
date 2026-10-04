<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Models\Partial;
use Aybarsm\Kong\AdminApi\Models\PartialEmbeddings;
use Aybarsm\Kong\AdminApi\Models\PartialFactory;
use Aybarsm\Kong\AdminApi\Models\PartialLink;
use Aybarsm\Kong\AdminApi\Models\PartialModel;
use Aybarsm\Kong\AdminApi\Models\PartialRedisCe;
use Aybarsm\Kong\AdminApi\Models\PartialRedisCeInput;
use Aybarsm\Kong\AdminApi\Models\PartialRedisEe;
use Aybarsm\Kong\AdminApi\Models\PartialRedisEeInput;
use Aybarsm\Kong\AdminApi\Models\PartialVectordb;
use Aybarsm\Kong\AdminApi\Pagination\CountedPage;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Resources\Nested\PartialLinks;
use Aybarsm\Kong\AdminApi\Resources\Partials;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(Partials::class, PartialLinks::class, PartialFactory::class, CountedPage::class, PartialRedisCeInput::class);

it('maps every Partial variant by its type discriminator', function (string $fixture, string $class): void {
    /** @var class-string<Partial> $class */
    expect(PartialFactory::fromArray(Fixture::get($fixture)))->toBeInstanceOf($class);
})->with([
    'redis-ce' => ['partial_redis_ce', PartialRedisCe::class],
    'redis-ee' => ['partial_redis_ee', PartialRedisEe::class],
    'vectordb' => ['partial_vectordb', PartialVectordb::class],
    'embeddings' => ['partial_embeddings', PartialEmbeddings::class],
    'model' => ['partial_model', PartialModel::class],
]);

it('rejects a Partial with a missing or unknown type', function (array $payload, string $message): void {
    expect(fn (): Partial => PartialFactory::fromArray($payload))->toThrow(UnexpectedResponseException::class, $message);
})->with([
    'missing' => [['config' => []], 'Required field "type" is missing or null.'],
    'unknown' => [['type' => 'memcached', 'config' => []], 'Unknown Partial type "memcached".'],
]);

it('sends the variant type by default from each input', function (): void {
    expect((new PartialRedisCeInput(config: ['host' => 'redis']))->toArray())->toBe(['config' => ['host' => 'redis'], 'type' => 'redis-ce'])
        ->and((new PartialRedisEeInput())->toArray())->toBe(['type' => 'redis-ee']);
});

it('sends every operation to the spec path', function (string $operation, string $method, string $path, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => [Fixture::get('partial_redis_ce')]]),
        'delete' => MockKong::raw(204),
        default => MockKong::json(200, Fixture::get('partial_redis_ce')),
    });
    $partials = $kong->client->partials();

    match ($operation) {
        'list' => $partials->list(),
        'get' => $partials->get('p 1'),
        'create' => $partials->create(new PartialRedisCeInput(name: 'cache')),
        'update' => $partials->update('p 1', ['name' => 'cache']),
        'upsert' => $partials->upsert('p 1', new PartialRedisCeInput(name: 'cache')),
        'delete' => $partials->delete('p 1'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and((string) $kong->lastRequest()->getBody())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', '/partials', null],
    'get' => ['get', 'GET', '/partials/p%201', null],
    'create' => ['create', 'POST', '/partials', '{"type":"redis-ce","name":"cache"}'],
    'update' => ['update', 'PATCH', '/partials/p%201', '{"name":"cache"}'],
    'upsert' => ['upsert', 'PUT', '/partials/p%201', '{"type":"redis-ce","name":"cache"}'],
    'delete' => ['delete', 'DELETE', '/partials/p%201', null],
]);

it('lists and walks Partials of mixed variants', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('partial_redis_ce'), Fixture::get('partial_model')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('partial_vectordb')]]),
    );

    $all = iterator_to_array($kong->client->partials()->all(new ListOptions(size: 2)));

    expect($all[0])->toBeInstanceOf(PartialRedisCe::class)
        ->and($all[1])->toBeInstanceOf(PartialModel::class)
        ->and($all[2])->toBeInstanceOf(PartialVectordb::class)
        ->and($kong->queryAt(1))->toBe(['size' => '2', 'offset' => 'p2']);
});

it('prefixes the workspace', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('partial_model')));

    $kong->client->inWorkspace('team-a')->partials()->get('p');

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/team-a/partials/p');
});

it('lists linked plugins with the total count and walks them', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['count' => 3, 'data' => [Fixture::get('partial_link')], 'offset' => 'o2', 'next' => '/partials/p/links?offset=o2']),
        MockKong::json(200, ['data' => [Fixture::get('partial_link')], 'offset' => 'o3']),
        MockKong::json(200, ['data' => [Fixture::get('partial_link')]]),
    );
    $links = $kong->client->inWorkspace('team-a')->partials()->links('p 1');

    $page = $links->list(new ListOptions(size: 1));
    $all = iterator_to_array($links->all(new ListOptions(size: 1, offset: 'o2')));

    expect($page)->toEqual(new CountedPage([PartialLink::fromArray(Fixture::get('partial_link'))], 3, 'o2', '/partials/p/links?offset=o2'))
        ->and($page->hasMore())->toBeTrue()
        ->and($kong->requestAt(0)->getUri()->getPath())->toBe('/team-a/partials/p%201/links')
        ->and($kong->queryAt(0))->toBe(['size' => '1'])
        ->and($all)->toHaveCount(2)
        ->and($kong->queryAt(2))->toBe(['size' => '1', 'offset' => 'o3']);
});

it('reports no more pages on the last counted page', function (): void {
    expect(CountedPage::fromArray(['data' => []], PartialLink::fromArray(...))->hasMore())->toBeFalse();
});

it('rejects empty IDs and maps 404', function (): void {
    $kong = MockKong::queue(MockKong::raw(404));

    expect(fn (): PartialLinks => $kong->client->partials()->links(''))->toThrow(InvalidArgumentException::class, 'Partial ID must not be empty.')
        ->and(fn (): Partial => $kong->client->partials()->get(''))->toThrow(InvalidArgumentException::class)
        ->and(fn (): Partial => $kong->client->partials()->get('missing'))->toThrow(NotFoundException::class, 'GET /partials/missing failed with HTTP 404.');
});
