<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\KongClient;
use Aybarsm\Kong\AdminApi\Models\Jwt;
use Aybarsm\Kong\AdminApi\Models\JwtInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\TagFilter;
use Aybarsm\Kong\AdminApi\Resources\Jwts;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(Jwts::class);

function jwtsUnderTest(KongClient $client): Jwts
{
    return $client->jwts();
}

it('sends every operation to the spec path', function (string $operation, string $method, string $path, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => [Fixture::get('jwt')]]),
        'delete' => MockKong::raw(204),
        default => MockKong::json(200, Fixture::get('jwt')),
    });
    $resource = jwtsUnderTest($kong->client);

    match ($operation) {
        'list' => $resource->list(),
        'get' => $resource->get('item 1'),
        'create' => $resource->create(['algorithm' => 'value']),
        'update' => $resource->update('item 1', ['algorithm' => 'value']),
        'upsert' => $resource->upsert('item 1', new JwtInput()),
        'delete' => $resource->delete('item 1'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and($kong->lastRequest()->getBody()->__toString())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', '/jwts', null],
    'get' => ['get', 'GET', '/jwts/item%201', null],
    'create' => ['create', 'POST', '/jwts', '{"algorithm":"value"}'],
    'update' => ['update', 'PATCH', '/jwts/item%201', '{"algorithm":"value"}'],
    'upsert' => ['upsert', 'PUT', '/jwts/item%201', '{}'],
    'delete' => ['delete', 'DELETE', '/jwts/item%201', null],
]);

it('lists one page with options and walks every page', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('jwt')], 'offset' => 'p1']),
        MockKong::json(200, ['data' => [Fixture::get('jwt')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('jwt')]]),
    );
    $resource = jwtsUnderTest($kong->client);

    $page = $resource->list(new ListOptions(size: 1, tags: TagFilter::allOf('a', 'b')));
    $rest = iterator_to_array($resource->all(new ListOptions(size: 1, offset: 'p1')));

    expect($page->data)->toEqual([Jwt::fromArray(Fixture::get('jwt'))])
        ->and($page->offset)->toBe('p1')
        ->and($kong->queryAt(0))->toBe(['size' => '1', 'tags' => 'a,b'])
        ->and($rest)->toHaveCount(2)
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p1'])
        ->and($kong->queryAt(2))->toBe(['size' => '1', 'offset' => 'p2'])
        ->and($kong->requestAt(2)->getUri()->getPath())->toBe('/jwts');
});

it('fetches the next page with the same filters, and stops after the last', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('jwt')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('jwt')]]),
    );
    $resource = jwtsUnderTest($kong->client);
    $options = new ListOptions(size: 1, tags: TagFilter::allOf('a'));

    $next = $resource->nextPage($resource->list($options), $options);

    expect($next?->data)->toHaveCount(1)
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p2', 'tags' => 'a'])
        ->and($next === null ? 'none' : $resource->nextPage($next, $options))->toBeNull()
        ->and($kong->requestCount())->toBe(2);
});

it('maps the response to Jwt', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('jwt')));

    expect(jwtsUnderTest($kong->client)->get('x'))->toEqual(Jwt::fromArray(Fixture::get('jwt')));
});

it('prefixes the workspace', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('jwt')));

    jwtsUnderTest($kong->client->inWorkspace('team-a'))->get('x');

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/team-a/jwts/x');
});

it('rejects an empty ID before sending anything', function (): void {
    $kong = MockKong::queue();

    expect(fn (): Jwt => jwtsUnderTest($kong->client)->get(''))->toThrow(InvalidArgumentException::class)
        ->and($kong->requestCount())->toBe(0);
});

it('maps 404 to NotFoundException', function (): void {
    $kong = MockKong::queue(MockKong::raw(404));

    expect(fn (): Jwt => jwtsUnderTest($kong->client)->get('missing'))
        ->toThrow(NotFoundException::class, 'GET /jwts/missing failed with HTTP 404.');
});
