<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\KongClient;
use Aybarsm\Kong\AdminApi\Models\RbacRoleEntity;
use Aybarsm\Kong\AdminApi\Models\RbacRoleEntityInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\TagFilter;
use Aybarsm\Kong\AdminApi\Resources\Nested\WorkspaceRbacRoleEntities;
use Aybarsm\Kong\AdminApi\Resources\WorkspaceRbacRoles;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(WorkspaceRbacRoleEntities::class, WorkspaceRbacRoles::class);

function workspaceRbacRoleEntitiesUnderNestedTest(KongClient $client): WorkspaceRbacRoleEntities
{
    return $client->workspaceRbacRoles()->entities('parent 1');
}

it('sends every operation to the spec path', function (string $operation, string $method, string $path, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => [Fixture::get('rbac_role_entity')]]),
        'delete' => MockKong::raw(204),
        default => MockKong::json(200, Fixture::get('rbac_role_entity')),
    });
    $resource = workspaceRbacRoleEntitiesUnderNestedTest($kong->client);

    match ($operation) {
        'list' => $resource->list(),
        'get' => $resource->get('item 1'),
        'create' => $resource->create(['actions' => 'value']),
        'update' => $resource->update('item 1', ['actions' => 'value']),
        'upsert' => $resource->upsert('item 1', new RbacRoleEntityInput()),
        'delete' => $resource->delete('item 1'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and($kong->lastRequest()->getBody()->__toString())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', '/default/rbac/roles/parent%201/entities', null],
    'get' => ['get', 'GET', '/default/rbac/roles/parent%201/entities/item%201', null],
    'create' => ['create', 'POST', '/default/rbac/roles/parent%201/entities', '{"actions":"value"}'],
    'update' => ['update', 'PATCH', '/default/rbac/roles/parent%201/entities/item%201', '{"actions":"value"}'],
    'upsert' => ['upsert', 'PUT', '/default/rbac/roles/parent%201/entities/item%201', '{}'],
    'delete' => ['delete', 'DELETE', '/default/rbac/roles/parent%201/entities/item%201', null],
]);

it('lists one page with options and walks every page', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('rbac_role_entity')], 'offset' => 'p1']),
        MockKong::json(200, ['data' => [Fixture::get('rbac_role_entity')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('rbac_role_entity')]]),
    );
    $resource = workspaceRbacRoleEntitiesUnderNestedTest($kong->client);

    $page = $resource->list(new ListOptions(size: 1, tags: TagFilter::allOf('a', 'b')));
    $rest = iterator_to_array($resource->all(new ListOptions(size: 1, offset: 'p1')));

    expect($page->data)->toEqual([RbacRoleEntity::fromArray(Fixture::get('rbac_role_entity'))])
        ->and($page->offset)->toBe('p1')
        ->and($kong->queryAt(0))->toBe(['size' => '1', 'tags' => 'a,b'])
        ->and($rest)->toHaveCount(2)
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p1'])
        ->and($kong->queryAt(2))->toBe(['size' => '1', 'offset' => 'p2'])
        ->and($kong->requestAt(2)->getUri()->getPath())->toBe('/default/rbac/roles/parent%201/entities');
});

it('maps the response to RbacRoleEntity', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('rbac_role_entity')));

    expect(workspaceRbacRoleEntitiesUnderNestedTest($kong->client)->get('x'))->toEqual(RbacRoleEntity::fromArray(Fixture::get('rbac_role_entity')));
});

it('prefixes the workspace', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('rbac_role_entity')));

    workspaceRbacRoleEntitiesUnderNestedTest($kong->client->inWorkspace('team-a'))->get('x');

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/team-a/rbac/roles/parent%201/entities/x');
});

it('rejects an empty ID before sending anything', function (): void {
    $kong = MockKong::queue();

    expect(fn (): RbacRoleEntity => workspaceRbacRoleEntitiesUnderNestedTest($kong->client)->get(''))->toThrow(InvalidArgumentException::class)
        ->and($kong->requestCount())->toBe(0);
});

it('maps 404 to NotFoundException', function (): void {
    $kong = MockKong::queue(MockKong::raw(404));

    expect(fn (): RbacRoleEntity => workspaceRbacRoleEntitiesUnderNestedTest($kong->client)->get('missing'))
        ->toThrow(NotFoundException::class, 'GET /default/rbac/roles/parent%201/entities/missing failed with HTTP 404.');
});

it('rejects an empty parent ID before sending anything', function (): void {
    expect(fn (): WorkspaceRbacRoleEntities => MockKong::queue()->client->workspaceRbacRoles()->entities(''))
        ->toThrow(InvalidArgumentException::class, 'RBAC role ID must not be empty.');
});
