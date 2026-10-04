<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * A Group as returned by the Admin API (spec schema `Group`).
 */
#[Schema('Group')]
final readonly class Group implements Model
{
    /**
     * @param string      $name      The name of the group
     * @param string|null $comment   Any comments associated with the specific group.
     * @param int|null    $createdAt Unix epoch when the resource was created.
     * @param string|null $id        A string representing a UUID (universally unique identifier).
     * @param int|null    $updatedAt Unix epoch when the resource was last updated.
     */
    public function __construct(
        public string $name,
        public ?string $comment = null,
        public ?int $createdAt = null,
        public ?string $id = null,
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
            'updated_at' => $this->updatedAt,
        ]);
    }
}
