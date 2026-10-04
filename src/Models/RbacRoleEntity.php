<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * An RBAC Role entity permission as returned by the Admin API (spec schema `RBACRoleEntity`).
 */
#[Schema('RBACRoleEntity')]
final readonly class RbacRoleEntity implements Model
{
    /**
     * @param list<string>    $actions
     * @param string          $entityId   The ID of the entity associated with the RBAC role.
     * @param string          $entityType The type of the entity associated with the RBAC role.
     * @param string|null     $comment    Additional comment or description for the RBAC role entity.
     * @param int|null        $createdAt  Unix epoch when the resource was created.
     * @param bool|null       $negative   Indicates whether the RBAC role has negative permissions for the entity.
     * @param ForeignKey|null $role       The RBAC role associated with the entity.
     * @param int|null        $updatedAt  Unix epoch when the resource was last updated.
     */
    public function __construct(
        public array $actions,
        public string $entityId,
        public string $entityType,
        public ?string $comment = null,
        public ?int $createdAt = null,
        public ?bool $negative = null,
        public ?ForeignKey $role = null,
        public ?int $updatedAt = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $role = Data::mapOrNull($data, 'role');

        return new self(
            actions: Data::stringList($data, 'actions'),
            entityId: Data::string($data, 'entity_id'),
            entityType: Data::string($data, 'entity_type'),
            comment: Data::stringOrNull($data, 'comment'),
            createdAt: Data::intOrNull($data, 'created_at'),
            negative: Data::boolOrNull($data, 'negative'),
            role: $role === null ? null : ForeignKey::fromArray($role),
            updatedAt: Data::intOrNull($data, 'updated_at'),
        );
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
            'role' => $this->role?->toArray(),
            'updated_at' => $this->updatedAt,
        ]);
    }
}
