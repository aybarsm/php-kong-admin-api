<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\KongClient;
use Aybarsm\Kong\AdminApi\Models\DegraphqlRoute;
use Aybarsm\Kong\AdminApi\Models\DegraphqlRouteInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\TagFilter;
use Aybarsm\Kong\AdminApi\Resources\Nested\ServiceDegraphqlRoutes;
use Aybarsm\Kong\AdminApi\Resources\Services;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(ServiceDegraphqlRoutes::class, Services::class);

function serviceDegraphqlRoutesUnderNestedTest(KongClient $client): ServiceDegraphqlRoutes
{
    return $client->services()->degraphqlRoutes('parent 1');
}

it('sends every operation to the spec path', function (string $operation, string $method, string $path, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => [Fixture::get('degraphql_route')]]),
        'delete' => MockKong::raw(204),
        default => MockKong::json(200, Fixture::get('degraphql_route')),
    });
    $resource = serviceDegraphqlRoutesUnderNestedTest($kong->client);

    match ($operation) {
        'list' => $resource->list(),
        'get' => $resource->get('item 1'),
        'create' => $resource->create(['query' => 'value']),
        'update' => $resource->update('item 1', ['query' => 'value']),
        'upsert' => $resource->upsert('item 1', new DegraphqlRouteInput()),
        'delete' => $resource->delete('item 1'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and($kong->lastRequest()->getBody()->__toString())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', '/services/parent%201/degraphql/routes', null],
    'get' => ['get', 'GET', '/services/parent%201/degraphql/routes/item%201', null],
    'create' => ['create', 'POST', '/services/parent%201/degraphql/routes', '{"query":"value"}'],
    'update' => ['update', 'PATCH', '/services/parent%201/degraphql/routes/item%201', '{"query":"value"}'],
    'upsert' => ['upsert', 'PUT', '/services/parent%201/degraphql/routes/item%201', '{}'],
    'delete' => ['delete', 'DELETE', '/services/parent%201/degraphql/routes/item%201', null],
]);

it('lists one page with options and walks every page', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('degraphql_route')], 'offset' => 'p1']),
        MockKong::json(200, ['data' => [Fixture::get('degraphql_route')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('degraphql_route')]]),
    );
    $resource = serviceDegraphqlRoutesUnderNestedTest($kong->client);

    $page = $resource->list(new ListOptions(size: 1, tags: TagFilter::allOf('a', 'b')));
    $rest = iterator_to_array($resource->all(new ListOptions(size: 1, offset: 'p1')));

    expect($page->data)->toEqual([DegraphqlRoute::fromArray(Fixture::get('degraphql_route'))])
        ->and($page->offset)->toBe('p1')
        ->and($kong->queryAt(0))->toBe(['size' => '1', 'tags' => 'a,b'])
        ->and($rest)->toHaveCount(2)
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p1'])
        ->and($kong->queryAt(2))->toBe(['size' => '1', 'offset' => 'p2'])
        ->and($kong->requestAt(2)->getUri()->getPath())->toBe('/services/parent%201/degraphql/routes');
});

it('maps the response to DegraphqlRoute', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('degraphql_route')));

    expect(serviceDegraphqlRoutesUnderNestedTest($kong->client)->get('x'))->toEqual(DegraphqlRoute::fromArray(Fixture::get('degraphql_route')));
});

it('prefixes the workspace', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('degraphql_route')));

    serviceDegraphqlRoutesUnderNestedTest($kong->client->inWorkspace('team-a'))->get('x');

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/team-a/services/parent%201/degraphql/routes/x');
});

it('rejects an empty ID before sending anything', function (): void {
    $kong = MockKong::queue();

    expect(fn (): DegraphqlRoute => serviceDegraphqlRoutesUnderNestedTest($kong->client)->get(''))->toThrow(InvalidArgumentException::class)
        ->and($kong->requestCount())->toBe(0);
});

it('maps 404 to NotFoundException', function (): void {
    $kong = MockKong::queue(MockKong::raw(404));

    expect(fn (): DegraphqlRoute => serviceDegraphqlRoutesUnderNestedTest($kong->client)->get('missing'))
        ->toThrow(NotFoundException::class, 'GET /services/parent%201/degraphql/routes/missing failed with HTTP 404.');
});

it('rejects an empty parent ID before sending anything', function (): void {
    expect(fn (): ServiceDegraphqlRoutes => MockKong::queue()->client->services()->degraphqlRoutes(''))
        ->toThrow(InvalidArgumentException::class, 'Service ID or name must not be empty.');
});
