<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `roles[]` object of AdminRoles (spec `#/components/responses/AdminRolesCreated/content/application~1json/schema.roles[]`).
 */
#[Schema('#/components/responses/AdminRolesCreated/content/application~1json/schema/properties/roles')]
final readonly class AdminRole implements Model
{
    /**
     * @param string|null $comment
     * @param int|null    $createdAt
     * @param string|null $id
     * @param bool|null   $isDefault
     * @param string|null $name
     */
    public function __construct(
        public ?string $comment = null,
        public ?int $createdAt = null,
        public ?string $id = null,
        public ?bool $isDefault = null,
        public ?string $name = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            comment: Data::stringOrNull($data, 'comment'),
            createdAt: Data::intOrNull($data, 'created_at'),
            id: Data::stringOrNull($data, 'id'),
            isDefault: Data::boolOrNull($data, 'is_default'),
            name: Data::stringOrNull($data, 'name'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'comment' => $this->comment,
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'is_default' => $this->isDefault,
            'name' => $this->name,
        ]);
    }
}
