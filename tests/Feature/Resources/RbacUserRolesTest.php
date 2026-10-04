<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\KongClient;
use Aybarsm\Kong\AdminApi\Models\RbacUserRole;
use Aybarsm\Kong\AdminApi\Models\RbacUserRoleInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\TagFilter;
use Aybarsm\Kong\AdminApi\Resources\RbacUserRoles;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(RbacUserRoles::class);

function rbacUserRolesUnderTest(KongClient $client): RbacUserRoles
{
    return $client->rbacUserRoles();
}

it('sends every operation to the spec path', function (string $operation, string $method, string $path, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => [Fixture::get('rbac_user_role')]]),
        'delete' => MockKong::raw(204),
        default => MockKong::json(200, Fixture::get('rbac_user_role')),
    });
    $resource = rbacUserRolesUnderTest($kong->client);

    match ($operation) {
        'list' => $resource->list(),
        'get' => $resource->get('item 1'),
        'create' => $resource->create(['role' => 'value']),
        'update' => $resource->update('item 1', ['role' => 'value']),
        'upsert' => $resource->upsert('item 1', new RbacUserRoleInput()),
        'delete' => $resource->delete('item 1'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and($kong->lastRequest()->getBody()->__toString())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', '/rbac_user_roles', null],
    'get' => ['get', 'GET', '/rbac_user_roles/item%201', null],
    'create' => ['create', 'POST', '/rbac_user_roles', '{"role":"value"}'],
    'update' => ['update', 'PATCH', '/rbac_user_roles/item%201', '{"role":"value"}'],
    'upsert' => ['upsert', 'PUT', '/rbac_user_roles/item%201', '{}'],
    'delete' => ['delete', 'DELETE', '/rbac_user_roles/item%201', null],
]);

it('lists one page with options and walks every page', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('rbac_user_role')], 'offset' => 'p1']),
        MockKong::json(200, ['data' => [Fixture::get('rbac_user_role')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('rbac_user_role')]]),
    );
    $resource = rbacUserRolesUnderTest($kong->client);

    $page = $resource->list(new ListOptions(size: 1, tags: TagFilter::allOf('a', 'b')));
    $rest = iterator_to_array($resource->all(new ListOptions(size: 1, offset: 'p1')));

    expect($page->data)->toEqual([RbacUserRole::fromArray(Fixture::get('rbac_user_role'))])
        ->and($page->offset)->toBe('p1')
        ->and($kong->queryAt(0))->toBe(['size' => '1', 'tags' => 'a,b'])
        ->and($rest)->toHaveCount(2)
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p1'])
        ->and($kong->queryAt(2))->toBe(['size' => '1', 'offset' => 'p2'])
        ->and($kong->requestAt(2)->getUri()->getPath())->toBe('/rbac_user_roles');
});

it('maps the response to RbacUserRole', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('rbac_user_role')));

    expect(rbacUserRolesUnderTest($kong->client)->get('x'))->toEqual(RbacUserRole::fromArray(Fixture::get('rbac_user_role')));
});

it('prefixes the workspace', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('rbac_user_role')));

    rbacUserRolesUnderTest($kong->client->inWorkspace('team-a'))->get('x');

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/team-a/rbac_user_roles/x');
});

it('rejects an empty ID before sending anything', function (): void {
    $kong = MockKong::queue();

    expect(fn (): RbacUserRole => rbacUserRolesUnderTest($kong->client)->get(''))->toThrow(InvalidArgumentException::class)
        ->and($kong->requestCount())->toBe(0);
});

it('maps 404 to NotFoundException', function (): void {
    $kong = MockKong::queue(MockKong::raw(404));

    expect(fn (): RbacUserRole => rbacUserRolesUnderTest($kong->client)->get('missing'))
        ->toThrow(NotFoundException::class, 'GET /rbac_user_roles/missing failed with HTTP 404.');
});
