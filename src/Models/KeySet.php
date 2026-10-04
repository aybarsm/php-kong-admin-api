<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * A Key Set as returned by the Admin API (spec schema `KeySet`).
 */
#[Schema('KeySet')]
final readonly class KeySet implements Model
{
    /**
     * @param int|null          $createdAt Unix epoch when the resource was created.
     * @param string|null       $id        A string representing a UUID (universally unique identifier).
     * @param string|null       $name      The name to associate with the given key-set.
     * @param list<string>|null $tags      A set of strings representing tags.
     * @param int|null          $updatedAt Unix epoch when the resource was last updated.
     */
    public function __construct(
        public ?int $createdAt = null,
        public ?string $id = null,
        public ?string $name = null,
        public ?array $tags = null,
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
            createdAt: Data::intOrNull($data, 'created_at'),
            id: Data::stringOrNull($data, 'id'),
            name: Data::stringOrNull($data, 'name'),
            tags: Data::stringListOrNull($data, 'tags'),
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
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'name' => $this->name,
            'tags' => $this->tags,
            'updated_at' => $this->updatedAt,
        ]);
    }
}
