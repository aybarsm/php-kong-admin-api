<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\KongClient;
use Aybarsm\Kong\AdminApi\Models\Target;
use Aybarsm\Kong\AdminApi\Models\TargetInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\TagFilter;
use Aybarsm\Kong\AdminApi\Resources\Nested\UpstreamTargets;
use Aybarsm\Kong\AdminApi\Resources\Upstreams;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(UpstreamTargets::class, Upstreams::class);

function upstreamTargetsUnderNestedTest(KongClient $client): UpstreamTargets
{
    return $client->upstreams()->targets('parent 1');
}

it('sends every operation to the spec path', function (string $operation, string $method, string $path, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => [Fixture::get('target')]]),
        'delete' => MockKong::raw(204),
        default => MockKong::json(200, Fixture::get('target')),
    });
    $resource = upstreamTargetsUnderNestedTest($kong->client);

    match ($operation) {
        'list' => $resource->list(),
        'get' => $resource->get('item 1'),
        'create' => $resource->create(['target' => 'value']),
        'update' => $resource->update('item 1', ['target' => 'value']),
        'upsert' => $resource->upsert('item 1', new TargetInput()),
        'delete' => $resource->delete('item 1'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and($kong->lastRequest()->getBody()->__toString())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', '/upstreams/parent%201/targets', null],
    'get' => ['get', 'GET', '/upstreams/parent%201/targets/item%201', null],
    'create' => ['create', 'POST', '/upstreams/parent%201/targets', '{"target":"value"}'],
    'update' => ['update', 'PATCH', '/upstreams/parent%201/targets/item%201', '{"target":"value"}'],
    'upsert' => ['upsert', 'PUT', '/upstreams/parent%201/targets/item%201', '{}'],
    'delete' => ['delete', 'DELETE', '/upstreams/parent%201/targets/item%201', null],
]);

it('lists one page with options and walks every page', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('target')], 'offset' => 'p1']),
        MockKong::json(200, ['data' => [Fixture::get('target')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('target')]]),
    );
    $resource = upstreamTargetsUnderNestedTest($kong->client);

    $page = $resource->list(new ListOptions(size: 1, tags: TagFilter::allOf('a', 'b')));
    $rest = iterator_to_array($resource->all(new ListOptions(size: 1, offset: 'p1')));

    expect($page->data)->toEqual([Target::fromArray(Fixture::get('target'))])
        ->and($page->offset)->toBe('p1')
        ->and($kong->queryAt(0))->toBe(['size' => '1', 'tags' => 'a,b'])
        ->and($rest)->toHaveCount(2)
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p1'])
        ->and($kong->queryAt(2))->toBe(['size' => '1', 'offset' => 'p2'])
        ->and($kong->requestAt(2)->getUri()->getPath())->toBe('/upstreams/parent%201/targets');
});

it('fetches the next page with the same filters, and stops after the last', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('target')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('target')]]),
    );
    $resource = upstreamTargetsUnderNestedTest($kong->client);
    $options = new ListOptions(size: 1, tags: TagFilter::allOf('a'));

    $next = $resource->nextPage($resource->list($options), $options);

    expect($next?->data)->toHaveCount(1)
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p2', 'tags' => 'a'])
        ->and($next === null ? 'none' : $resource->nextPage($next, $options))->toBeNull()
        ->and($kong->requestCount())->toBe(2);
});

it('maps the response to Target', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('target')));

    expect(upstreamTargetsUnderNestedTest($kong->client)->get('x'))->toEqual(Target::fromArray(Fixture::get('target')));
});

it('prefixes the workspace', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('target')));

    upstreamTargetsUnderNestedTest($kong->client->inWorkspace('team-a'))->get('x');

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/team-a/upstreams/parent%201/targets/x');
});

it('rejects an empty ID before sending anything', function (): void {
    $kong = MockKong::queue();

    expect(fn (): Target => upstreamTargetsUnderNestedTest($kong->client)->get(''))->toThrow(InvalidArgumentException::class)
        ->and($kong->requestCount())->toBe(0);
});

it('maps 404 to NotFoundException', function (): void {
    $kong = MockKong::queue(MockKong::raw(404));

    expect(fn (): Target => upstreamTargetsUnderNestedTest($kong->client)->get('missing'))
        ->toThrow(NotFoundException::class, 'GET /upstreams/parent%201/targets/missing failed with HTTP 404.');
});

it('rejects an empty parent ID before sending anything', function (): void {
    expect(fn (): UpstreamTargets => MockKong::queue()->client->upstreams()->targets(''))
        ->toThrow(InvalidArgumentException::class, 'Upstream ID must not be empty.');
});
