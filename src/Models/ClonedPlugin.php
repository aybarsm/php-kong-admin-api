<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * A Cloned Plugin as returned by the Admin API (spec schema `ClonedPlugin`).
 */
#[Schema('ClonedPlugin')]
final readonly class ClonedPlugin implements Model
{
    /**
     * @param string            $name      The name to associate with the cloned plugin.
     * @param string            $ref       The name of the base plugin that this cloned plugin references.
     * @param int|null          $createdAt Unix epoch when the resource was created.
     * @param string|null       $id        A string representing a UUID (universally unique identifier).
     * @param int|null          $priority  The plugin execution priority.
     * @param list<string>|null $tags      A set of strings representing tags.
     * @param int|null          $updatedAt Unix epoch when the resource was last updated.
     */
    public function __construct(
        public string $name,
        public string $ref,
        public ?int $createdAt = null,
        public ?string $id = null,
        public ?int $priority = null,
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
            name: Data::string($data, 'name'),
            ref: Data::string($data, 'ref'),
            createdAt: Data::intOrNull($data, 'created_at'),
            id: Data::stringOrNull($data, 'id'),
            priority: Data::intOrNull($data, 'priority'),
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
            'name' => $this->name,
            'ref' => $this->ref,
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'priority' => $this->priority,
            'tags' => $this->tags,
            'updated_at' => $this->updatedAt,
        ]);
    }
}
