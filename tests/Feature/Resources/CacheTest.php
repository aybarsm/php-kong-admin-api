<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\Models\CacheEntry;
use Aybarsm\Kong\AdminApi\Resources\Cache;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(Cache::class);

it('sends every operation to the spec path, never workspace-prefixed', function (string $operation, string $method, string $path): void {
    $kong = MockKong::queue($operation === 'get' ? MockKong::json(200, Fixture::get('cache_entry')) : MockKong::raw(204));
    $cache = $kong->client->inWorkspace('team-a')->cache();

    match ($operation) {
        'get' => $cache->get('services:a b'),
        'delete' => $cache->delete('services:a b'),
        'flush' => $cache->flush(),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path);
})->with([
    'get' => ['get', 'GET', '/cache/services%3Aa%20b'],
    'delete' => ['delete', 'DELETE', '/cache/services%3Aa%20b'],
    'flush' => ['flush', 'DELETE', '/cache'],
]);

it('maps a cache entry and a missing key', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('cache_entry')), MockKong::json(404, ['message' => 'Not found']));

    expect($kong->client->cache()->get('k'))->toEqual(CacheEntry::fromArray(Fixture::get('cache_entry')))
        ->and(fn (): CacheEntry => $kong->client->cache()->get('missing'))->toThrow(NotFoundException::class, 'Not found');
});

it('rejects an empty key', function (): void {
    expect(fn (): CacheEntry => MockKong::queue()->client->cache()->get(''))->toThrow(InvalidArgumentException::class);
});
