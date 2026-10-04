<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * A role assigned to a Group (spec schema `GroupRole`, also the `GroupRoleRequest` body).
 */
#[Schema('GroupRole')]
final readonly class GroupRole implements Model
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
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $group = Data::mapOrNull($data, 'group');
        $rbacRole = Data::mapOrNull($data, 'rbac_role');

        return new self(
            group: $group === null ? null : GroupRoleGroup::fromArray($group),
            groupId: Data::stringOrNull($data, 'group_id'),
            rbacRole: $rbacRole === null ? null : GroupRoleRbacRole::fromArray($rbacRole),
            roleId: Data::stringOrNull($data, 'role_id'),
            workspace: Data::stringOrNull($data, 'workspace'),
        );
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
