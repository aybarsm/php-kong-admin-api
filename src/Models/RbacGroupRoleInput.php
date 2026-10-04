<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * Request body for creating, updating or upserting an RBAC group-role assignment
 * (spec schema `RBACGroupRole`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('RBACGroupRole')]
final readonly class RbacGroupRoleInput implements Input
{
    /**
     * @param int|null               $createdAt Unix epoch when the resource was created.
     * @param ForeignKey|string|null $group     The group associated with the RBAC role
     * @param ForeignKey|string|null $rbacRole  The RBAC role
     * @param int|null               $updatedAt Unix epoch when the resource was last updated.
     * @param ForeignKey|string|null $workspace The workspace associated with the RBAC role.
     */
    public function __construct(
        public ?int $createdAt = null,
        public ForeignKey|string|null $group = null,
        public ForeignKey|string|null $rbacRole = null,
        public ?int $updatedAt = null,
        public ForeignKey|string|null $workspace = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'created_at' => $this->createdAt,
            'group' => $this->group === null ? null : ForeignKey::of($this->group)->toArray(),
            'rbac_role' => $this->rbacRole === null ? null : ForeignKey::of($this->rbacRole)->toArray(),
            'updated_at' => $this->updatedAt,
            'workspace' => $this->workspace === null ? null : ForeignKey::of($this->workspace)->toArray(),
        ]);
    }
}
