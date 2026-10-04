<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\Models\Route;
use Aybarsm\Kong\AdminApi\Models\RouteExpression;
use Aybarsm\Kong\AdminApi\Models\RouteJson;
use Aybarsm\Kong\AdminApi\Models\RouteJsonInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Resources\Nested\ServiceRoutes;
use Aybarsm\Kong\AdminApi\Resources\Services;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(ServiceRoutes::class, Services::class);

it('sends every operation under the service path', function (string $operation, string $method, string $path, ?array $body): void {
    /** @var array<string, mixed>|null $body */
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => [Fixture::get('route_json')]]),
        'delete' => MockKong::raw(204),
        default => MockKong::json(200, Fixture::get('route_json')),
    });
    $routes = $kong->client->services()->routes('my service');

    match ($operation) {
        'list' => $routes->list(new ListOptions(size: 5)),
        'get' => $routes->get('r1'),
        'create' => $routes->create(new RouteJsonInput(paths: ['/v1'])),
        'update' => $routes->update('r1', ['strip_path' => false]),
        'upsert' => $routes->upsert('r1', new RouteJsonInput(hosts: ['a.test'])),
        'delete' => $routes->delete('r1'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path);
    if ($body !== null) {
        expect($kong->lastJsonBody())->toBe($body);
    }
})->with([
    'list' => ['list', 'GET', '/services/my%20service/routes', null],
    'get' => ['get', 'GET', '/services/my%20service/routes/r1', null],
    'create' => ['create', 'POST', '/services/my%20service/routes', ['paths' => ['/v1']]],
    'update' => ['update', 'PATCH', '/services/my%20service/routes/r1', ['strip_path' => false]],
    'upsert' => ['upsert', 'PUT', '/services/my%20service/routes/r1', ['hosts' => ['a.test']]],
    'delete' => ['delete', 'DELETE', '/services/my%20service/routes/r1', null],
]);

it('sends the list query and maps both variants', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('route_json')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('route_expression')]]),
    );

    $all = iterator_to_array($kong->client->services()->routes('svc')->all(new ListOptions(size: 1)));

    expect($all[0])->toBeInstanceOf(RouteJson::class)
        ->and($all[1])->toBeInstanceOf(RouteExpression::class)
        ->and($kong->queryAt(0))->toBe(['size' => '1'])
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p2'])
        ->and($kong->requestAt(1)->getUri()->getPath())->toBe('/services/svc/routes');
});

it('prefixes the workspace before the service path', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('route_json')));

    $kong->client->inWorkspace('team-a')->services()->routes('svc')->get('r1');

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/team-a/services/svc/routes/r1');
});

it('rejects an empty service ID or name', function (): void {
    expect(fn (): ServiceRoutes => MockKong::queue()->client->services()->routes(''))
        ->toThrow(InvalidArgumentException::class, 'Service ID or name must not be empty.');
});

it('maps 404 for a missing service route', function (): void {
    $kong = MockKong::queue(MockKong::raw(404));

    expect(fn (): Route => $kong->client->services()->routes('svc')->get('missing'))
        ->toThrow(NotFoundException::class, 'GET /services/svc/routes/missing failed with HTTP 404.');
});
