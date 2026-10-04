<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\KongClient;
use Aybarsm\Kong\AdminApi\Models\BasicAuth;
use Aybarsm\Kong\AdminApi\Models\BasicAuthInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\TagFilter;
use Aybarsm\Kong\AdminApi\Resources\Consumers;
use Aybarsm\Kong\AdminApi\Resources\Nested\ConsumerBasicAuths;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(ConsumerBasicAuths::class, Consumers::class);

function consumerBasicAuthsUnderNestedTest(KongClient $client): ConsumerBasicAuths
{
    return $client->consumers()->basicAuths('parent 1');
}

it('sends every operation to the spec path', function (string $operation, string $method, string $path, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => [Fixture::get('basic_auth')]]),
        'delete' => MockKong::raw(204),
        default => MockKong::json(200, Fixture::get('basic_auth')),
    });
    $resource = consumerBasicAuthsUnderNestedTest($kong->client);

    match ($operation) {
        'list' => $resource->list(),
        'get' => $resource->get('item 1'),
        'create' => $resource->create(['password' => 'value']),
        'update' => $resource->update('item 1', ['password' => 'value']),
        'upsert' => $resource->upsert('item 1', new BasicAuthInput()),
        'delete' => $resource->delete('item 1'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and($kong->lastRequest()->getBody()->__toString())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', '/consumers/parent%201/basic-auth', null],
    'get' => ['get', 'GET', '/consumers/parent%201/basic-auth/item%201', null],
    'create' => ['create', 'POST', '/consumers/parent%201/basic-auth', '{"password":"value"}'],
    'update' => ['update', 'PATCH', '/consumers/parent%201/basic-auth/item%201', '{"password":"value"}'],
    'upsert' => ['upsert', 'PUT', '/consumers/parent%201/basic-auth/item%201', '{}'],
    'delete' => ['delete', 'DELETE', '/consumers/parent%201/basic-auth/item%201', null],
]);

it('lists one page with options and walks every page', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('basic_auth')], 'offset' => 'p1']),
        MockKong::json(200, ['data' => [Fixture::get('basic_auth')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('basic_auth')]]),
    );
    $resource = consumerBasicAuthsUnderNestedTest($kong->client);

    $page = $resource->list(new ListOptions(size: 1, tags: TagFilter::allOf('a', 'b')));
    $rest = iterator_to_array($resource->all(new ListOptions(size: 1, offset: 'p1')));

    expect($page->data)->toEqual([BasicAuth::fromArray(Fixture::get('basic_auth'))])
        ->and($page->offset)->toBe('p1')
        ->and($kong->queryAt(0))->toBe(['size' => '1', 'tags' => 'a,b'])
        ->and($rest)->toHaveCount(2)
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p1'])
        ->and($kong->queryAt(2))->toBe(['size' => '1', 'offset' => 'p2'])
        ->and($kong->requestAt(2)->getUri()->getPath())->toBe('/consumers/parent%201/basic-auth');
});

it('maps the response to BasicAuth', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('basic_auth')));

    expect(consumerBasicAuthsUnderNestedTest($kong->client)->get('x'))->toEqual(BasicAuth::fromArray(Fixture::get('basic_auth')));
});

it('prefixes the workspace', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('basic_auth')));

    consumerBasicAuthsUnderNestedTest($kong->client->inWorkspace('team-a'))->get('x');

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/team-a/consumers/parent%201/basic-auth/x');
});

it('rejects an empty ID before sending anything', function (): void {
    $kong = MockKong::queue();

    expect(fn (): BasicAuth => consumerBasicAuthsUnderNestedTest($kong->client)->get(''))->toThrow(InvalidArgumentException::class)
        ->and($kong->requestCount())->toBe(0);
});

it('maps 404 to NotFoundException', function (): void {
    $kong = MockKong::queue(MockKong::raw(404));

    expect(fn (): BasicAuth => consumerBasicAuthsUnderNestedTest($kong->client)->get('missing'))
        ->toThrow(NotFoundException::class, 'GET /consumers/parent%201/basic-auth/missing failed with HTTP 404.');
});

it('rejects an empty parent ID before sending anything', function (): void {
    expect(fn (): ConsumerBasicAuths => MockKong::queue()->client->consumers()->basicAuths(''))
        ->toThrow(InvalidArgumentException::class, 'Consumer ID must not be empty.');
});
