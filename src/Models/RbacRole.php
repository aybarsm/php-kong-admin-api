<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * An RBAC Role as returned by the Admin API (spec schema `RBACRole`).
 */
#[Schema('RBACRole')]
final readonly class RbacRole implements Model
{
    /**
     * @param string      $name      The name of the RBAC role.
     * @param string|null $comment   Additional comment or description for the RBAC role.
     * @param int|null    $createdAt Unix epoch when the resource was created.
     * @param string|null $id        A string representing a UUID (universally unique identifier).
     * @param bool|null   $isDefault Indicates whether the RBAC role is the default role.
     * @param int|null    $updatedAt Unix epoch when the resource was last updated.
     */
    public function __construct(
        public string $name,
        public ?string $comment = null,
        public ?int $createdAt = null,
        public ?string $id = null,
        public ?bool $isDefault = null,
        public ?int $updatedAt = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            name: Data::string($data, 'name'),
            comment: Data::stringOrNull($data, 'comment'),
            createdAt: Data::intOrNull($data, 'created_at'),
            id: Data::stringOrNull($data, 'id'),
            isDefault: Data::boolOrNull($data, 'is_default'),
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
            'name' => $this->name,
            'comment' => $this->comment,
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'is_default' => $this->isDefault,
            'updated_at' => $this->updatedAt,
        ]);
    }
}
