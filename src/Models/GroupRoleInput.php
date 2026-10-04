<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Request body for creating, updating or upserting a role assigned to a Group (spec schema `GroupRole`, also the `GroupRoleRequest` body).
 * (spec schema `GroupRole`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('GroupRole')]
final readonly class GroupRoleInput implements Input
{
    /**
     * @param GroupRoleGroup|null    $group
     * @param string|null            $groupId   ID of the group.
     * @param GroupRoleRbacRole|null $rbacRole
     * @param string|null            $roleId    ID of the RBAC role assigned to the group.
     * @param string|null            $workspace Workspace ID.
     */
    public function __construct(
        public ?GroupRoleGroup $group = null,
        public ?string $groupId = null,
        public ?GroupRoleRbacRole $rbacRole = null,
        public ?string $roleId = null,
        public ?string $workspace = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'group' => $this->group?->toArray(),
            'group_id' => $this->groupId,
            'rbac_role' => $this->rbacRole?->toArray(),
            'role_id' => $this->roleId,
            'workspace' => $this->workspace,
        ]);
    }
}
