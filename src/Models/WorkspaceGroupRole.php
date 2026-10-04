<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * A role assigned to a workspace group (spec response `GetRolesResponse`).
 *
 * `POST /workspace_/groups/{groups}/roles` returns the same shape (`GroupRoleAssociationCreated`).
 */
#[Schema('#/components/responses/GetRolesResponse/content/application~1json/schema')]
final readonly class WorkspaceGroupRole implements Model
{
    /**
     * @param WorkspaceGroupRoleGroup|null    $group
     * @param WorkspaceGroupRoleRbacRole|null $rbacRole
     * @param ForeignKey|null                 $workspace
     */
    public function __construct(
        public ?WorkspaceGroupRoleGroup $group = null,
        public ?WorkspaceGroupRoleRbacRole $rbacRole = null,
        public ?ForeignKey $workspace = null,
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
        $workspace = Data::mapOrNull($data, 'workspace');

        return new self(
            group: $group === null ? null : WorkspaceGroupRoleGroup::fromArray($group),
            rbacRole: $rbacRole === null ? null : WorkspaceGroupRoleRbacRole::fromArray($rbacRole),
            workspace: $workspace === null ? null : ForeignKey::fromArray($workspace),
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
            'rbac_role' => $this->rbacRole?->toArray(),
            'workspace' => $this->workspace?->toArray(),
        ]);
    }
}
