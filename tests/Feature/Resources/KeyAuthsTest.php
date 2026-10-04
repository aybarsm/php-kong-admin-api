<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\KongClient;
use Aybarsm\Kong\AdminApi\Models\KeyAuth;
use Aybarsm\Kong\AdminApi\Models\KeyAuthInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\TagFilter;
use Aybarsm\Kong\AdminApi\Resources\KeyAuths;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(KeyAuths::class);

function keyAuthsUnderTest(KongClient $client): KeyAuths
{
    return $client->keyAuths();
}

it('sends every operation to the spec path', function (string $operation, string $method, string $path, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => [Fixture::get('key_auth')]]),
        'delete' => MockKong::raw(204),
        default => MockKong::json(200, Fixture::get('key_auth')),
    });
    $resource = keyAuthsUnderTest($kong->client);

    match ($operation) {
        'list' => $resource->list(),
        'get' => $resource->get('item 1'),
        'create' => $resource->create(['consumer' => 'value']),
        'update' => $resource->update('item 1', ['consumer' => 'value']),
        'upsert' => $resource->upsert('item 1', new KeyAuthInput()),
        'delete' => $resource->delete('item 1'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and($kong->lastRequest()->getBody()->__toString())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', '/key-auths', null],
    'get' => ['get', 'GET', '/key-auths/item%201', null],
    'create' => ['create', 'POST', '/key-auths', '{"consumer":"value"}'],
    'update' => ['update', 'PATCH', '/key-auths/item%201', '{"consumer":"value"}'],
    'upsert' => ['upsert', 'PUT', '/key-auths/item%201', '{}'],
    'delete' => ['delete', 'DELETE', '/key-auths/item%201', null],
]);

it('lists one page with options and walks every page', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('key_auth')], 'offset' => 'p1']),
        MockKong::json(200, ['data' => [Fixture::get('key_auth')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('key_auth')]]),
    );
    $resource = keyAuthsUnderTest($kong->client);

    $page = $resource->list(new ListOptions(size: 1, tags: TagFilter::allOf('a', 'b')));
    $rest = iterator_to_array($resource->all(new ListOptions(size: 1, offset: 'p1')));

    expect($page->data)->toEqual([KeyAuth::fromArray(Fixture::get('key_auth'))])
        ->and($page->offset)->toBe('p1')
        ->and($kong->queryAt(0))->toBe(['size' => '1', 'tags' => 'a,b'])
        ->and($rest)->toHaveCount(2)
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p1'])
        ->and($kong->queryAt(2))->toBe(['size' => '1', 'offset' => 'p2'])
        ->and($kong->requestAt(2)->getUri()->getPath())->toBe('/key-auths');
});

it('maps the response to KeyAuth', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('key_auth')));

    expect(keyAuthsUnderTest($kong->client)->get('x'))->toEqual(KeyAuth::fromArray(Fixture::get('key_auth')));
});

it('prefixes the workspace', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('key_auth')));

    keyAuthsUnderTest($kong->client->inWorkspace('team-a'))->get('x');

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/team-a/key-auths/x');
});

it('rejects an empty ID before sending anything', function (): void {
    $kong = MockKong::queue();

    expect(fn (): KeyAuth => keyAuthsUnderTest($kong->client)->get(''))->toThrow(InvalidArgumentException::class)
        ->and($kong->requestCount())->toBe(0);
});

it('maps 404 to NotFoundException', function (): void {
    $kong = MockKong::queue(MockKong::raw(404));

    expect(fn (): KeyAuth => keyAuthsUnderTest($kong->client)->get('missing'))
        ->toThrow(NotFoundException::class, 'GET /key-auths/missing failed with HTTP 404.');
});
