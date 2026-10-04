<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `group` object of GroupRole (spec `GroupRole.group`).
 */
#[Schema('#/components/schemas/GroupRole/properties/group')]
final readonly class GroupRoleGroup implements Model
{
    /**
     * @param string|null $comment
     * @param string|null $id
     * @param string|null $name
     * @param string|null $updatedAt
     */
    public function __construct(
        public ?string $comment = null,
        public ?string $id = null,
        public ?string $name = null,
        public ?string $updatedAt = null,
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
            id: Data::stringOrNull($data, 'id'),
            name: Data::stringOrNull($data, 'name'),
            updatedAt: Data::stringOrNull($data, 'updated_at'),
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
            'id' => $this->id,
            'name' => $this->name,
            'updated_at' => $this->updatedAt,
        ]);
    }
}
