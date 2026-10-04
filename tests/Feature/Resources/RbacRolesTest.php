<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\KongClient;
use Aybarsm\Kong\AdminApi\Models\RbacRole;
use Aybarsm\Kong\AdminApi\Models\RbacRoleInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\TagFilter;
use Aybarsm\Kong\AdminApi\Resources\RbacRoles;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(RbacRoles::class);

function rbacRolesUnderTest(KongClient $client): RbacRoles
{
    return $client->rbacRoles();
}

it('sends every operation to the spec path', function (string $operation, string $method, string $path, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => [Fixture::get('rbac_role')]]),
        'delete' => MockKong::raw(204),
        default => MockKong::json(200, Fixture::get('rbac_role')),
    });
    $resource = rbacRolesUnderTest($kong->client);

    match ($operation) {
        'list' => $resource->list(),
        'get' => $resource->get('item 1'),
        'create' => $resource->create(['name' => 'value']),
        'update' => $resource->update('item 1', ['name' => 'value']),
        'upsert' => $resource->upsert('item 1', new RbacRoleInput()),
        'delete' => $resource->delete('item 1'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and($kong->lastRequest()->getBody()->__toString())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', '/rbac_roles', null],
    'get' => ['get', 'GET', '/rbac_roles/item%201', null],
    'create' => ['create', 'POST', '/rbac_roles', '{"name":"value"}'],
    'update' => ['update', 'PATCH', '/rbac_roles/item%201', '{"name":"value"}'],
    'upsert' => ['upsert', 'PUT', '/rbac_roles/item%201', '{}'],
    'delete' => ['delete', 'DELETE', '/rbac_roles/item%201', null],
]);

it('lists one page with options and walks every page', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('rbac_role')], 'offset' => 'p1']),
        MockKong::json(200, ['data' => [Fixture::get('rbac_role')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('rbac_role')]]),
    );
    $resource = rbacRolesUnderTest($kong->client);

    $page = $resource->list(new ListOptions(size: 1, tags: TagFilter::allOf('a', 'b')));
    $rest = iterator_to_array($resource->all(new ListOptions(size: 1, offset: 'p1')));

    expect($page->data)->toEqual([RbacRole::fromArray(Fixture::get('rbac_role'))])
        ->and($page->offset)->toBe('p1')
        ->and($kong->queryAt(0))->toBe(['size' => '1', 'tags' => 'a,b'])
        ->and($rest)->toHaveCount(2)
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p1'])
        ->and($kong->queryAt(2))->toBe(['size' => '1', 'offset' => 'p2'])
        ->and($kong->requestAt(2)->getUri()->getPath())->toBe('/rbac_roles');
});

it('maps the response to RbacRole', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('rbac_role')));

    expect(rbacRolesUnderTest($kong->client)->get('x'))->toEqual(RbacRole::fromArray(Fixture::get('rbac_role')));
});

it('never prefixes a workspace on these global-only paths', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('rbac_role')));

    rbacRolesUnderTest($kong->client->inWorkspace('team-a'))->get('x');

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/rbac_roles/x');
});

it('rejects an empty ID before sending anything', function (): void {
    $kong = MockKong::queue();

    expect(fn (): RbacRole => rbacRolesUnderTest($kong->client)->get(''))->toThrow(InvalidArgumentException::class)
        ->and($kong->requestCount())->toBe(0);
});

it('maps 404 to NotFoundException', function (): void {
    $kong = MockKong::queue(MockKong::raw(404));

    expect(fn (): RbacRole => rbacRolesUnderTest($kong->client)->get('missing'))
        ->toThrow(NotFoundException::class, 'GET /rbac_roles/missing failed with HTTP 404.');
});
