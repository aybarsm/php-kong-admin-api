<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * An RBAC group-role assignment as returned by the Admin API (spec schema `RBACGroupRole`).
 */
#[Schema('RBACGroupRole')]
final readonly class RbacGroupRole implements Model
{
    /**
     * @param int|null        $createdAt Unix epoch when the resource was created.
     * @param ForeignKey|null $group     The group associated with the RBAC role
     * @param ForeignKey|null $rbacRole  The RBAC role
     * @param int|null        $updatedAt Unix epoch when the resource was last updated.
     * @param ForeignKey|null $workspace The workspace associated with the RBAC role.
     */
    public function __construct(
        public ?int $createdAt = null,
        public ?ForeignKey $group = null,
        public ?ForeignKey $rbacRole = null,
        public ?int $updatedAt = null,
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
            createdAt: Data::intOrNull($data, 'created_at'),
            group: $group === null ? null : ForeignKey::fromArray($group),
            rbacRole: $rbacRole === null ? null : ForeignKey::fromArray($rbacRole),
            updatedAt: Data::intOrNull($data, 'updated_at'),
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
            'created_at' => $this->createdAt,
            'group' => $this->group?->toArray(),
            'rbac_role' => $this->rbacRole?->toArray(),
            'updated_at' => $this->updatedAt,
            'workspace' => $this->workspace?->toArray(),
        ]);
    }
}
