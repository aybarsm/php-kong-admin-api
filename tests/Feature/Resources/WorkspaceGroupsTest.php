<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Models\GroupRoleInput;
use Aybarsm\Kong\AdminApi\Models\WorkspaceGroup;
use Aybarsm\Kong\AdminApi\Models\WorkspaceGroupInput;
use Aybarsm\Kong\AdminApi\Models\WorkspaceGroupRole;
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;
use Aybarsm\Kong\AdminApi\Resources\WorkspaceGroups;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(WorkspaceGroups::class, AbstractResource::class, WorkspaceGroupInput::class);

it('sends every operation to the literal workspace_ paths (spec-notes Q3)', function (string $operation, string $method, string $path, string $query, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, [Fixture::get('workspace_group')]),
        'roles' => MockKong::json(200, [Fixture::get('workspace_group_role')]),
        'create' => MockKong::json(201, Fixture::get('workspace_group')),
        'addRole' => MockKong::json(201, Fixture::get('workspace_group_role')),
        'update' => MockKong::raw(200),
        default => MockKong::raw(204),
    });
    $groups = $kong->client->inWorkspace('team-a')->workspaceGroups();

    match ($operation) {
        'list' => $groups->list(),
        'create' => $groups->create(new WorkspaceGroupInput(name: 'ops')),
        'update' => $groups->update('ops team', ['name' => 'ops']),
        'roles' => $groups->roles('ops team'),
        'addRole' => $groups->addRole('ops team', new GroupRoleInput(roleId: 'r1', workspace: 'w1')),
        'removeRole' => $groups->removeRole('ops team', 'r1', 'w1'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and($kong->lastRequest()->getUri()->getQuery())->toBe($query)
        ->and((string) $kong->lastRequest()->getBody())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', '/workspace_/groups', '', null],
    'create' => ['create', 'POST', '/workspace_/groups', '', '{"name":"ops"}'],
    'update' => ['update', 'PATCH', '/workspace_/groups/ops%20team', '', '{"name":"ops"}'],
    'roles' => ['roles', 'GET', '/workspace_/groups/ops%20team/roles', '', null],
    'addRole' => ['addRole', 'POST', '/workspace_/groups/ops%20team/roles', '', '{"role_id":"r1","workspace":"w1"}'],
    'removeRole' => ['removeRole', 'DELETE', '/workspace_/groups/ops%20team/roles', 'rbac_role_id=r1&workspace_id=w1', null],
]);

it('maps the bare-array list responses', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, [Fixture::get('workspace_group'), Fixture::get('workspace_group')]),
        MockKong::json(200, [Fixture::get('workspace_group_role')]),
        MockKong::json(200, []),
    );
    $groups = $kong->client->workspaceGroups();

    expect($groups->list())->toEqual([WorkspaceGroup::fromArray(Fixture::get('workspace_group')), WorkspaceGroup::fromArray(Fixture::get('workspace_group'))])
        ->and($groups->roles('g'))->toEqual([WorkspaceGroupRole::fromArray(Fixture::get('workspace_group_role'))])
        ->and($groups->list())->toBe([]);
});

it('rejects an object where the spec defines a bare array', function (): void {
    $kong = MockKong::queue(MockKong::json(200, ['data' => [Fixture::get('workspace_group')]]));

    expect(fn (): array => $kong->client->workspaceGroups()->list())
        ->toThrow(UnexpectedResponseException::class, 'GET /workspace_/groups returned a JSON object where an array was expected.');
});

it('rejects empty arguments before sending anything', function (): void {
    $kong = MockKong::queue();
    $groups = $kong->client->workspaceGroups();

    expect(fn () => $groups->removeRole('g', '', 'w'))->toThrow(InvalidArgumentException::class, 'RBAC role ID must not be empty.')
        ->and(fn () => $groups->removeRole('g', 'r', ''))->toThrow(InvalidArgumentException::class, 'Workspace ID must not be empty.')
        ->and(fn (): array => $groups->roles(''))->toThrow(InvalidArgumentException::class)
        ->and($kong->requestCount())->toBe(0);
});
