<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\GroupRoleInput;
use Aybarsm\Kong\AdminApi\Models\WorkspaceGroup;
use Aybarsm\Kong\AdminApi\Models\WorkspaceGroupInput;
use Aybarsm\Kong\AdminApi\Models\WorkspaceGroupRole;

/**
 * Workspace groups (spec tag "Workspaces"): `/workspace_/groups`, `/workspace_/groups/{groups}` and
 * `/workspace_/groups/{groups}/roles`.
 *
 * The literal path segment `workspace_` is implemented exactly as the spec names it (spec-notes Q3).
 * List responses are bare JSON arrays, not the `{data, next, offset}` envelope.
 */
final readonly class WorkspaceGroups extends AbstractResource
{
    private const string SEGMENT = 'workspace_';

    /**
     * List the groups (operationId `list-groups`).
     *
     * @return list<WorkspaceGroup>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/workspace_/groups', 'list-groups', OperationScope::GlobalOnly)]
    public function list(): array
    {
        return array_map(WorkspaceGroup::fromArray(...), $this->items(Transport::METHOD_GET, $this->path(OperationScope::GlobalOnly, self::SEGMENT, 'groups')));
    }

    /**
     * Create a group (operationId `create-group-in-workspace`, body `UpdateGroupsRequest`).
     *
     * @param WorkspaceGroupInput|array<string, mixed> $group
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/workspace_/groups', 'create-group-in-workspace', OperationScope::GlobalOnly)]
    public function create(WorkspaceGroupInput|array $group): WorkspaceGroup
    {
        return WorkspaceGroup::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, 'groups'),
            body: $group,
        ));
    }

    /**
     * Rename a group (operationId `update-workspace-group`, PATCH, body `UpdateGroupsRequest`). The spec
     * defines no response body (spec-notes Q15).
     *
     * @param WorkspaceGroupInput|array<string, mixed> $group
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $groupIdOrName is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/workspace_/groups/{groups}', 'update-workspace-group', OperationScope::GlobalOnly)]
    public function update(string $groupIdOrName, WorkspaceGroupInput|array $group): void
    {
        $this->none(Transport::METHOD_PATCH, $this->path(OperationScope::GlobalOnly, self::SEGMENT, 'groups', $groupIdOrName), body: $group);
    }

    /**
     * List the roles of a group (operationId `list-group-roles`).
     *
     * @return list<WorkspaceGroupRole>
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $groupIdOrName is empty
     */
    #[Operation(Transport::METHOD_GET, '/workspace_/groups/{groups}/roles', 'list-group-roles', OperationScope::GlobalOnly)]
    public function roles(string $groupIdOrName): array
    {
        return array_map(
            WorkspaceGroupRole::fromArray(...),
            $this->items(Transport::METHOD_GET, $this->path(OperationScope::GlobalOnly, self::SEGMENT, 'groups', $groupIdOrName, 'roles')),
        );
    }

    /**
     * Assign a role to a group (operationId `create-role-to-group`, body `GroupRoleRequest`).
     *
     * @param GroupRoleInput|array<string, mixed> $role
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $groupIdOrName is empty
     */
    #[Operation(Transport::METHOD_POST, '/workspace_/groups/{groups}/roles', 'create-role-to-group', OperationScope::GlobalOnly)]
    public function addRole(string $groupIdOrName, GroupRoleInput|array $role): WorkspaceGroupRole
    {
        return WorkspaceGroupRole::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, 'groups', $groupIdOrName, 'roles'),
            body: $role,
        ));
    }

    /**
     * Remove a role from a group (operationId `delete-role-from-group`). Both query parameters are required
     * by the spec.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when an argument is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/workspace_/groups/{groups}/roles', 'delete-role-from-group', OperationScope::GlobalOnly)]
    public function removeRole(string $groupIdOrName, string $rbacRoleId, string $workspaceId): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::GlobalOnly, self::SEGMENT, 'groups', $groupIdOrName, 'roles'), [
            'rbac_role_id' => $this->required($rbacRoleId, 'RBAC role ID'),
            'workspace_id' => $this->required($workspaceId, 'Workspace ID'),
        ]);
    }
}
