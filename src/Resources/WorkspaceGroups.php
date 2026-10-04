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
 * `/workspace_/groups/{groups}/roles`. These are separate from RBAC Groups (`/groups`) and from Consumer Groups
 * (`/consumer_groups`), and are never `/{workspace}`-prefixed.
 *
 * Enterprise-only. Paths are taken verbatim from the Gateway Admin EE 3.16 spec, including the literal prefix
 * `/workspace_/groups` (not `/workspaces/{workspace}/groups` and not `/groups`). The rendered spec and its curl
 * examples use that string. It is not in Kong open-source and has not been verified against a running Kong
 * Enterprise node. If a future spec revision changes the path, follow the spec; do not keep a local rewrite.
 * (spec-notes Q3)
 *
 * List responses are bare JSON arrays, not the `{data, next, offset}` envelope.
 *
 * @warning The `/workspace_/groups` path is taken verbatim from the spec and is unverified against a running node.
 */
final readonly class WorkspaceGroups extends AbstractResource
{
    /** The spec's path prefix for this resource; a spec revision that changes it is a one-line change here. */
    public const string PATH = '/workspace_/groups';

    /**
     * List the groups (operationId `list-groups`).
     *
     * @return list<WorkspaceGroup>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, self::PATH, 'list-groups', OperationScope::GlobalOnly)]
    public function list(): array
    {
        return array_map(WorkspaceGroup::fromArray(...), $this->items(Transport::METHOD_GET, $this->path(OperationScope::GlobalOnly, ...self::segments())));
    }

    /**
     * Create a group (operationId `create-group-in-workspace`, body `UpdateGroupsRequest`).
     *
     * @param WorkspaceGroupInput|array<string, mixed> $group
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, self::PATH, 'create-group-in-workspace', OperationScope::GlobalOnly)]
    public function create(WorkspaceGroupInput|array $group): WorkspaceGroup
    {
        return WorkspaceGroup::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::GlobalOnly, ...self::segments()),
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
     * @throws InvalidArgumentException when $groups is empty
     */
    #[Operation(Transport::METHOD_PATCH, self::PATH . '/{groups}', 'update-workspace-group', OperationScope::GlobalOnly)]
    public function update(string $groups, WorkspaceGroupInput|array $group): void
    {
        $this->none(Transport::METHOD_PATCH, $this->path(OperationScope::GlobalOnly, ...[...self::segments(), $groups]), body: $group);
    }

    /**
     * List the roles of a group (operationId `list-group-roles`).
     *
     * @return list<WorkspaceGroupRole>
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $groups is empty
     */
    #[Operation(Transport::METHOD_GET, self::PATH . '/{groups}/roles', 'list-group-roles', OperationScope::GlobalOnly)]
    public function roles(string $groups): array
    {
        return array_map(
            WorkspaceGroupRole::fromArray(...),
            $this->items(Transport::METHOD_GET, $this->path(OperationScope::GlobalOnly, ...[...self::segments(), $groups, 'roles'])),
        );
    }

    /**
     * Assign a role to a group (operationId `create-role-to-group`, body `GroupRoleRequest`).
     *
     * @param GroupRoleInput|array<string, mixed> $role
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $groups is empty
     */
    #[Operation(Transport::METHOD_POST, self::PATH . '/{groups}/roles', 'create-role-to-group', OperationScope::GlobalOnly)]
    public function addRole(string $groups, GroupRoleInput|array $role): WorkspaceGroupRole
    {
        return WorkspaceGroupRole::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::GlobalOnly, ...[...self::segments(), $groups, 'roles']),
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
    #[Operation(Transport::METHOD_DELETE, self::PATH . '/{groups}/roles', 'delete-role-from-group', OperationScope::GlobalOnly)]
    public function removeRole(string $groups, string $rbacRoleId, string $workspaceId): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::GlobalOnly, ...[...self::segments(), $groups, 'roles']), [
            'rbac_role_id' => $this->required($rbacRoleId, 'RBAC role ID'),
            'workspace_id' => $this->required($workspaceId, 'Workspace ID'),
        ]);
    }

    /**
     * PATH split into raw path segments (`['workspace_', 'groups']`), encoded by path().
     *
     * @return list<string>
     */
    private static function segments(): array
    {
        return explode('/', ltrim(self::PATH, '/'));
    }
}
