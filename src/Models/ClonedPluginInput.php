<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Request body for creating, updating or upserting a Cloned Plugin
 * (spec schema `ClonedPlugin`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('ClonedPlugin')]
final readonly class ClonedPluginInput implements Input
{
    /**
     * @param string|null       $name      The name to associate with the cloned plugin. Required by the spec on create.
     * @param string|null       $ref       The name of the base plugin that this cloned plugin references. Required by the spec on create.
     * @param int|null          $createdAt Unix epoch when the resource was created.
     * @param string|null       $id        A string representing a UUID (universally unique identifier).
     * @param int|null          $priority  The plugin execution priority.
     * @param list<string>|null $tags      A set of strings representing tags.
     * @param int|null          $updatedAt Unix epoch when the resource was last updated.
     */
    public function __construct(
        public ?string $name = null,
        public ?string $ref = null,
        public ?int $createdAt = null,
        public ?string $id = null,
        public ?int $priority = null,
        public ?array $tags = null,
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
