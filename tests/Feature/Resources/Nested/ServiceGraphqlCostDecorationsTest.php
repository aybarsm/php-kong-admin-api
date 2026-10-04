<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\KongClient;
use Aybarsm\Kong\AdminApi\Models\GraphqlCostDecoration;
use Aybarsm\Kong\AdminApi\Models\GraphqlCostDecorationInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\TagFilter;
use Aybarsm\Kong\AdminApi\Resources\Nested\ServiceGraphqlCostDecorations;
use Aybarsm\Kong\AdminApi\Resources\Services;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(ServiceGraphqlCostDecorations::class, Services::class);

function serviceGraphqlCostDecorationsUnderNestedTest(KongClient $client): ServiceGraphqlCostDecorations
{
    return $client->services()->graphqlCostDecorations('parent 1');
}

it('sends every operation to the spec path', function (string $operation, string $method, string $path, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => [Fixture::get('graphql_cost_decoration')]]),
        'delete' => MockKong::raw(204),
        default => MockKong::json(200, Fixture::get('graphql_cost_decoration')),
    });
    $resource = serviceGraphqlCostDecorationsUnderNestedTest($kong->client);

    match ($operation) {
        'list' => $resource->list(),
        'get' => $resource->get('item 1'),
        'create' => $resource->create(['type_path' => 'value']),
        'update' => $resource->update('item 1', ['type_path' => 'value']),
        'upsert' => $resource->upsert('item 1', new GraphqlCostDecorationInput()),
        'delete' => $resource->delete('item 1'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and($kong->lastRequest()->getBody()->__toString())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', '/services/parent%201/graphql-rate-limiting-advanced/costs', null],
    'get' => ['get', 'GET', '/services/parent%201/graphql-rate-limiting-advanced/costs/item%201', null],
    'create' => ['create', 'POST', '/services/parent%201/graphql-rate-limiting-advanced/costs', '{"type_path":"value"}'],
    'update' => ['update', 'PATCH', '/services/parent%201/graphql-rate-limiting-advanced/costs/item%201', '{"type_path":"value"}'],
    'upsert' => ['upsert', 'PUT', '/services/parent%201/graphql-rate-limiting-advanced/costs/item%201', '{}'],
    'delete' => ['delete', 'DELETE', '/services/parent%201/graphql-rate-limiting-advanced/costs/item%201', null],
]);

it('lists one page with options and walks every page', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('graphql_cost_decoration')], 'offset' => 'p1']),
        MockKong::json(200, ['data' => [Fixture::get('graphql_cost_decoration')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('graphql_cost_decoration')]]),
    );
    $resource = serviceGraphqlCostDecorationsUnderNestedTest($kong->client);

    $page = $resource->list(new ListOptions(size: 1, tags: TagFilter::allOf('a', 'b')));
    $rest = iterator_to_array($resource->all(new ListOptions(size: 1, offset: 'p1')));

    expect($page->data)->toEqual([GraphqlCostDecoration::fromArray(Fixture::get('graphql_cost_decoration'))])
        ->and($page->offset)->toBe('p1')
        ->and($kong->queryAt(0))->toBe(['size' => '1', 'tags' => 'a,b'])
        ->and($rest)->toHaveCount(2)
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p1'])
        ->and($kong->queryAt(2))->toBe(['size' => '1', 'offset' => 'p2'])
        ->and($kong->requestAt(2)->getUri()->getPath())->toBe('/services/parent%201/graphql-rate-limiting-advanced/costs');
});

it('fetches the next page with the same filters, and stops after the last', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('graphql_cost_decoration')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('graphql_cost_decoration')]]),
    );
    $resource = serviceGraphqlCostDecorationsUnderNestedTest($kong->client);
    $options = new ListOptions(size: 1, tags: TagFilter::allOf('a'));

    $next = $resource->nextPage($resource->list($options), $options);

    expect($next?->data)->toHaveCount(1)
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p2', 'tags' => 'a'])
        ->and($next === null ? 'none' : $resource->nextPage($next, $options))->toBeNull()
        ->and($kong->requestCount())->toBe(2);
});

it('maps the response to GraphqlCostDecoration', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('graphql_cost_decoration')));

    expect(serviceGraphqlCostDecorationsUnderNestedTest($kong->client)->get('x'))->toEqual(GraphqlCostDecoration::fromArray(Fixture::get('graphql_cost_decoration')));
});

it('prefixes the workspace', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('graphql_cost_decoration')));

    serviceGraphqlCostDecorationsUnderNestedTest($kong->client->inWorkspace('team-a'))->get('x');

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/team-a/services/parent%201/graphql-rate-limiting-advanced/costs/x');
});

it('rejects an empty ID before sending anything', function (): void {
    $kong = MockKong::queue();

    expect(fn (): GraphqlCostDecoration => serviceGraphqlCostDecorationsUnderNestedTest($kong->client)->get(''))->toThrow(InvalidArgumentException::class)
        ->and($kong->requestCount())->toBe(0);
});

it('maps 404 to NotFoundException', function (): void {
    $kong = MockKong::queue(MockKong::raw(404));

    expect(fn (): GraphqlCostDecoration => serviceGraphqlCostDecorationsUnderNestedTest($kong->client)->get('missing'))
        ->toThrow(NotFoundException::class, 'GET /services/parent%201/graphql-rate-limiting-advanced/costs/missing failed with HTTP 404.');
});

it('rejects an empty parent ID before sending anything', function (): void {
    expect(fn (): ServiceGraphqlCostDecorations => MockKong::queue()->client->services()->graphqlCostDecorations(''))
        ->toThrow(InvalidArgumentException::class, 'Service ID or name must not be empty.');
});
