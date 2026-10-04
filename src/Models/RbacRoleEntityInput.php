<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * Request body for creating, updating or upserting an RBAC Role entity permission
 * (spec schema `RBACRoleEntity`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('RBACRoleEntity')]
final readonly class RbacRoleEntityInput implements Input
{
    /**
     * @param list<string>|null      $actions    Required by the spec on create.
     * @param string|null            $entityId   The ID of the entity associated with the RBAC role. Required by the spec on create.
     * @param string|null            $entityType The type of the entity associated with the RBAC role. Required by the spec on create.
     * @param string|null            $comment    Additional comment or description for the RBAC role entity.
     * @param int|null               $createdAt  Unix epoch when the resource was created.
     * @param bool|null              $negative   Indicates whether the RBAC role has negative permissions for the entity.
     * @param ForeignKey|string|null $role       The RBAC role associated with the entity.
     * @param int|null               $updatedAt  Unix epoch when the resource was last updated.
     */
    public function __construct(
        public ?array $actions = null,
        public ?string $entityId = null,
        public ?string $entityType = null,
        public ?string $comment = null,
        public ?int $createdAt = null,
        public ?bool $negative = null,
        public ForeignKey|string|null $role = null,
        public ?int $updatedAt = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'actions' => $this->actions,
            'entity_id' => $this->entityId,
            'entity_type' => $this->entityType,
            'comment' => $this->comment,
            'created_at' => $this->createdAt,
            'negative' => $this->negative,
            'role' => $this->role === null ? null : ForeignKey::of($this->role)->toArray(),
            'updated_at' => $this->updatedAt,
        ]);
    }
}
