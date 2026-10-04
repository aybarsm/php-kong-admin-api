<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\KongClient;
use Aybarsm\Kong\AdminApi\Models\Vault;
use Aybarsm\Kong\AdminApi\Models\VaultInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\TagFilter;
use Aybarsm\Kong\AdminApi\Resources\Vaults;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(Vaults::class);

function vaultsUnderTest(KongClient $client): Vaults
{
    return $client->vaults();
}

it('sends every operation to the spec path', function (string $operation, string $method, string $path, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => [Fixture::get('vault')]]),
        'delete' => MockKong::raw(204),
        default => MockKong::json(200, Fixture::get('vault')),
    });
    $resource = vaultsUnderTest($kong->client);

    match ($operation) {
        'list' => $resource->list(),
        'get' => $resource->get('item 1'),
        'create' => $resource->create(['name' => 'value']),
        'update' => $resource->update('item 1', ['name' => 'value']),
        'upsert' => $resource->upsert('item 1', new VaultInput()),
        'delete' => $resource->delete('item 1'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and($kong->lastRequest()->getBody()->__toString())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', '/vaults', null],
    'get' => ['get', 'GET', '/vaults/item%201', null],
    'create' => ['create', 'POST', '/vaults', '{"name":"value"}'],
    'update' => ['update', 'PATCH', '/vaults/item%201', '{"name":"value"}'],
    'upsert' => ['upsert', 'PUT', '/vaults/item%201', '{}'],
    'delete' => ['delete', 'DELETE', '/vaults/item%201', null],
]);

it('lists one page with options and walks every page', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('vault')], 'offset' => 'p1']),
        MockKong::json(200, ['data' => [Fixture::get('vault')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('vault')]]),
    );
    $resource = vaultsUnderTest($kong->client);

    $page = $resource->list(new ListOptions(size: 1, tags: TagFilter::allOf('a', 'b')));
    $rest = iterator_to_array($resource->all(new ListOptions(size: 1, offset: 'p1')));

    expect($page->data)->toEqual([Vault::fromArray(Fixture::get('vault'))])
        ->and($page->offset)->toBe('p1')
        ->and($kong->queryAt(0))->toBe(['size' => '1', 'tags' => 'a,b'])
        ->and($rest)->toHaveCount(2)
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p1'])
        ->and($kong->queryAt(2))->toBe(['size' => '1', 'offset' => 'p2'])
        ->and($kong->requestAt(2)->getUri()->getPath())->toBe('/vaults');
});

it('maps the response to Vault', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('vault')));

    expect(vaultsUnderTest($kong->client)->get('x'))->toEqual(Vault::fromArray(Fixture::get('vault')));
});

it('prefixes the workspace', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('vault')));

    vaultsUnderTest($kong->client->inWorkspace('team-a'))->get('x');

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/team-a/vaults/x');
});

it('rejects an empty ID before sending anything', function (): void {
    $kong = MockKong::queue();

    expect(fn (): Vault => vaultsUnderTest($kong->client)->get(''))->toThrow(InvalidArgumentException::class)
        ->and($kong->requestCount())->toBe(0);
});

it('maps 404 to NotFoundException', function (): void {
    $kong = MockKong::queue(MockKong::raw(404));

    expect(fn (): Vault => vaultsUnderTest($kong->client)->get('missing'))
        ->toThrow(NotFoundException::class, 'GET /vaults/missing failed with HTTP 404.');
});
