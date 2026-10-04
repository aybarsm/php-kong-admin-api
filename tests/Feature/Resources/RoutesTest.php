<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Enums\Protocol;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\Exceptions\UnauthorizedException;
use Aybarsm\Kong\AdminApi\Models\Route;
use Aybarsm\Kong\AdminApi\Models\RouteExpression;
use Aybarsm\Kong\AdminApi\Models\RouteExpressionInput;
use Aybarsm\Kong\AdminApi\Models\RouteJson;
use Aybarsm\Kong\AdminApi\Models\RouteJsonInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\TagFilter;
use Aybarsm\Kong\AdminApi\Resources\Routes;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(Routes::class);

it('lists a page mixing both route variants', function (): void {
    $kong = MockKong::queue(MockKong::json(200, [
        'data' => [Fixture::get('route_json'), Fixture::get('route_expression')],
        'offset' => 'next-page',
    ]));

    $page = $kong->client->routes()->list(new ListOptions(size: 2, tags: TagFilter::anyOf('a', 'b')));

    expect($kong->lastRequest()->getMethod())->toBe('GET')
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/routes')
        ->and($kong->lastQuery())->toBe(['size' => '2', 'tags' => 'a/b'])
        ->and($page->data[0])->toBeInstanceOf(RouteJson::class)
        ->and($page->data[1])->toBeInstanceOf(RouteExpression::class)
        ->and($page->offset)->toBe('next-page');
});

it('walks every page', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('route_json')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('route_expression')]]),
    );

    $all = iterator_to_array($kong->client->routes()->all());

    expect($all)->toHaveCount(2)
        ->and($kong->queryAt(1))->toBe(['offset' => 'p2']);
});

it('gets a route by ID or name', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('route_expression')));

    $route = $kong->client->routes()->get('expression-route');

    expect($kong->lastRequest()->getMethod())->toBe('GET')
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/routes/expression-route')
        ->and($route)->toBeInstanceOf(RouteExpression::class);
});

it('creates a route from either input variant or an array', function (RouteJsonInput|RouteExpressionInput|array $input, array $body): void {
    /** @var RouteJsonInput|RouteExpressionInput|array<string, mixed> $input */
    $kong = MockKong::queue(MockKong::json(201, Fixture::get('route_json')));

    $route = $kong->client->routes()->create($input);

    expect($kong->lastRequest()->getMethod())->toBe('POST')
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/routes')
        ->and($kong->lastJsonBody())->toBe($body)
        ->and($route)->toBeInstanceOf(Route::class);
})->with([
    'json input' => [new RouteJsonInput(paths: ['/v1'], service: 'svc'), ['paths' => ['/v1'], 'service' => ['id' => 'svc']]],
    'expression input' => [new RouteExpressionInput(expression: 'http.path == "/"', protocols: [Protocol::Http]), ['expression' => 'http.path == "/"', 'protocols' => ['http']]],
    'array' => [['hosts' => ['a.test'], 'service' => null], ['hosts' => ['a.test'], 'service' => null]],
]);

it('updates a route with PATCH', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('route_json')));

    $kong->client->routes()->update('example-route', new RouteJsonInput(stripPath: false));

    expect($kong->lastRequest()->getMethod())->toBe('PATCH')
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/routes/example-route')
        ->and($kong->lastJsonBody())->toBe(['strip_path' => false]);
});

it('upserts a route with PUT', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('route_expression')));

    $route = $kong->client->routes()->upsert('expression-route', new RouteExpressionInput(expression: 'http.path == "/"'));

    expect($kong->lastRequest()->getMethod())->toBe('PUT')
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/routes/expression-route')
        ->and($kong->lastJsonBody())->toBe(['expression' => 'http.path == "/"'])
        ->and($route)->toBeInstanceOf(RouteExpression::class);
});

it('deletes a route', function (): void {
    $kong = MockKong::queue(MockKong::raw(204));

    $kong->client->routes()->delete('example-route');

    expect($kong->lastRequest()->getMethod())->toBe('DELETE')
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/routes/example-route');
});

it('prefixes the workspace on every operation', function (string $operation): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => []]),
        'delete' => MockKong::raw(204),
        default => MockKong::json(200, Fixture::get('route_json')),
    });
    $routes = $kong->client->inWorkspace('team-a')->routes();

    match ($operation) {
        'list' => $routes->list(),
        'get' => $routes->get('r'),
        'create' => $routes->create(['paths' => ['/']]),
        'update' => $routes->update('r', ['paths' => ['/']]),
        'upsert' => $routes->upsert('r', ['paths' => ['/']]),
        'delete' => $routes->delete('r'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getUri()->getPath())->toStartWith('/team-a/routes');
})->with(['list', 'get', 'create', 'update', 'upsert', 'delete']);

it('maps 404 and 401', function (): void {
    $kong = MockKong::queue(MockKong::raw(404), MockKong::json(401, ['message' => 'Unauthorized', 'status' => 401]));

    expect(fn (): Route => $kong->client->routes()->get('missing'))->toThrow(NotFoundException::class)
        ->and(fn (): Route => $kong->client->routes()->get('x'))->toThrow(UnauthorizedException::class, 'Unauthorized');
});
