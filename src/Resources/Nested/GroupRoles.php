<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources\Nested;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\GroupRole;
use Aybarsm\Kong\AdminApi\Models\GroupRoleInput;
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;

/**
 * RBAC roles assigned to one Group (spec tag "Groups"): `/groups/{GroupId}/roles`.
 *
 * Obtain it with `$client->groups()->roles($groupId)`.
 */
final readonly class GroupRoles extends AbstractResource
{
    private const string PATH = '/groups/{GroupId}/roles';

    private const string SEGMENT = 'roles';

    /**
     * List the Group's roles (operationId `get-groups-group_id_or_name-roles`). The spec response is
     * `{data}` without pagination.
     *
     * @return list<GroupRole>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, self::PATH, 'get-groups-group_id_or_name-roles', OperationScope::GlobalOnly)]
    public function list(): array
    {
        $payload = $this->object(Transport::METHOD_GET, $this->path(OperationScope::GlobalOnly, self::SEGMENT));

        return array_map(GroupRole::fromArray(...), Data::listOfMaps($payload, 'data'));
    }

    /**
     * Assign a role to the Group (operationId `create-groups-group_id_or_name-roles`, body `GroupRoleRequest`).
     *
     * @param GroupRoleInput|array<string, mixed> $role
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, self::PATH, 'create-groups-group_id_or_name-roles', OperationScope::GlobalOnly)]
    public function add(GroupRoleInput|array $role): GroupRole
    {
        return GroupRole::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT),
            body: $role,
        ));
    }

    /**
     * Remove a role from the Group (operationId `delete-groups-group_id_or_name-roles`). Both query
     * parameters are required by the spec.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when an argument is empty
     */
    #[Operation(Transport::METHOD_DELETE, self::PATH, 'delete-groups-group_id_or_name-roles', OperationScope::GlobalOnly)]
    public function remove(string $rbacRoleId, string $workspaceId): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::GlobalOnly, self::SEGMENT), [
            'rbac_role_id' => $this->required($rbacRoleId, 'RBAC role ID'),
            'workspace_id' => $this->required($workspaceId, 'Workspace ID'),
        ]);
    }
}
