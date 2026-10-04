<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\KongClient;
use Aybarsm\Kong\AdminApi\Models\HmacAuth;
use Aybarsm\Kong\AdminApi\Models\HmacAuthInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\TagFilter;
use Aybarsm\Kong\AdminApi\Resources\HmacAuths;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(HmacAuths::class);

function hmacAuthsUnderTest(KongClient $client): HmacAuths
{
    return $client->hmacAuths();
}

it('sends every operation to the spec path', function (string $operation, string $method, string $path, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => [Fixture::get('hmac_auth')]]),
        'delete' => MockKong::raw(204),
        default => MockKong::json(200, Fixture::get('hmac_auth')),
    });
    $resource = hmacAuthsUnderTest($kong->client);

    match ($operation) {
        'list' => $resource->list(),
        'get' => $resource->get('item 1'),
        'create' => $resource->create(['username' => 'value']),
        'update' => $resource->update('item 1', ['username' => 'value']),
        'upsert' => $resource->upsert('item 1', new HmacAuthInput()),
        'delete' => $resource->delete('item 1'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and($kong->lastRequest()->getBody()->__toString())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', '/hmac-auths', null],
    'get' => ['get', 'GET', '/hmac-auths/item%201', null],
    'create' => ['create', 'POST', '/hmac-auths', '{"username":"value"}'],
    'update' => ['update', 'PATCH', '/hmac-auths/item%201', '{"username":"value"}'],
    'upsert' => ['upsert', 'PUT', '/hmac-auths/item%201', '{}'],
    'delete' => ['delete', 'DELETE', '/hmac-auths/item%201', null],
]);

it('lists one page with options and walks every page', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('hmac_auth')], 'offset' => 'p1']),
        MockKong::json(200, ['data' => [Fixture::get('hmac_auth')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('hmac_auth')]]),
    );
    $resource = hmacAuthsUnderTest($kong->client);

    $page = $resource->list(new ListOptions(size: 1, tags: TagFilter::allOf('a', 'b')));
    $rest = iterator_to_array($resource->all(new ListOptions(size: 1, offset: 'p1')));

    expect($page->data)->toEqual([HmacAuth::fromArray(Fixture::get('hmac_auth'))])
        ->and($page->offset)->toBe('p1')
        ->and($kong->queryAt(0))->toBe(['size' => '1', 'tags' => 'a,b'])
        ->and($rest)->toHaveCount(2)
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p1'])
        ->and($kong->queryAt(2))->toBe(['size' => '1', 'offset' => 'p2'])
        ->and($kong->requestAt(2)->getUri()->getPath())->toBe('/hmac-auths');
});

it('maps the response to HmacAuth', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('hmac_auth')));

    expect(hmacAuthsUnderTest($kong->client)->get('x'))->toEqual(HmacAuth::fromArray(Fixture::get('hmac_auth')));
});

it('prefixes the workspace', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('hmac_auth')));

    hmacAuthsUnderTest($kong->client->inWorkspace('team-a'))->get('x');

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/team-a/hmac-auths/x');
});

it('rejects an empty ID before sending anything', function (): void {
    $kong = MockKong::queue();

    expect(fn (): HmacAuth => hmacAuthsUnderTest($kong->client)->get(''))->toThrow(InvalidArgumentException::class)
        ->and($kong->requestCount())->toBe(0);
});

it('maps 404 to NotFoundException', function (): void {
    $kong = MockKong::queue(MockKong::raw(404));

    expect(fn (): HmacAuth => hmacAuthsUnderTest($kong->client)->get('missing'))
        ->toThrow(NotFoundException::class, 'GET /hmac-auths/missing failed with HTTP 404.');
});
