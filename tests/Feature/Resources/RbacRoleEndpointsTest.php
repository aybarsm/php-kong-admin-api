<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\KongClient;
use Aybarsm\Kong\AdminApi\Models\RbacRoleEndpoint;
use Aybarsm\Kong\AdminApi\Models\RbacRoleEndpointInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\TagFilter;
use Aybarsm\Kong\AdminApi\Resources\RbacRoleEndpoints;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(RbacRoleEndpoints::class);

function rbacRoleEndpointsUnderTest(KongClient $client): RbacRoleEndpoints
{
    return $client->rbacRoleEndpoints();
}

it('sends every operation to the spec path', function (string $operation, string $method, string $path, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => [Fixture::get('rbac_role_endpoint')]]),
        'delete' => MockKong::raw(204),
        default => MockKong::json(200, Fixture::get('rbac_role_endpoint')),
    });
    $resource = rbacRoleEndpointsUnderTest($kong->client);

    match ($operation) {
        'list' => $resource->list(),
        'get' => $resource->get('item 1'),
        'create' => $resource->create(['actions' => 'value']),
        'update' => $resource->update('item 1', ['actions' => 'value']),
        'upsert' => $resource->upsert('item 1', new RbacRoleEndpointInput()),
        'delete' => $resource->delete('item 1'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and($kong->lastRequest()->getBody()->__toString())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', '/rbac_role_endpoints', null],
    'get' => ['get', 'GET', '/rbac_role_endpoints/item%201', null],
    'create' => ['create', 'POST', '/rbac_role_endpoints', '{"actions":"value"}'],
    'update' => ['update', 'PATCH', '/rbac_role_endpoints/item%201', '{"actions":"value"}'],
    'upsert' => ['upsert', 'PUT', '/rbac_role_endpoints/item%201', '{}'],
    'delete' => ['delete', 'DELETE', '/rbac_role_endpoints/item%201', null],
]);

it('lists one page with options and walks every page', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('rbac_role_endpoint')], 'offset' => 'p1']),
        MockKong::json(200, ['data' => [Fixture::get('rbac_role_endpoint')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('rbac_role_endpoint')]]),
    );
    $resource = rbacRoleEndpointsUnderTest($kong->client);

    $page = $resource->list(new ListOptions(size: 1, tags: TagFilter::allOf('a', 'b')));
    $rest = iterator_to_array($resource->all(new ListOptions(size: 1, offset: 'p1')));

    expect($page->data)->toEqual([RbacRoleEndpoint::fromArray(Fixture::get('rbac_role_endpoint'))])
        ->and($page->offset)->toBe('p1')
        ->and($kong->queryAt(0))->toBe(['size' => '1', 'tags' => 'a,b'])
        ->and($rest)->toHaveCount(2)
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p1'])
        ->and($kong->queryAt(2))->toBe(['size' => '1', 'offset' => 'p2'])
        ->and($kong->requestAt(2)->getUri()->getPath())->toBe('/rbac_role_endpoints');
});

it('maps the response to RbacRoleEndpoint', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('rbac_role_endpoint')));

    expect(rbacRoleEndpointsUnderTest($kong->client)->get('x'))->toEqual(RbacRoleEndpoint::fromArray(Fixture::get('rbac_role_endpoint')));
});

it('never prefixes a workspace on these global-only paths', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('rbac_role_endpoint')));

    rbacRoleEndpointsUnderTest($kong->client->inWorkspace('team-a'))->get('x');

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/rbac_role_endpoints/x');
});

it('rejects an empty ID before sending anything', function (): void {
    $kong = MockKong::queue();

    expect(fn (): RbacRoleEndpoint => rbacRoleEndpointsUnderTest($kong->client)->get(''))->toThrow(InvalidArgumentException::class)
        ->and($kong->requestCount())->toBe(0);
});

it('maps 404 to NotFoundException', function (): void {
    $kong = MockKong::queue(MockKong::raw(404));

    expect(fn (): RbacRoleEndpoint => rbacRoleEndpointsUnderTest($kong->client)->get('missing'))
        ->toThrow(NotFoundException::class, 'GET /rbac_role_endpoints/missing failed with HTTP 404.');
});
