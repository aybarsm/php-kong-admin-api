<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Request body for creating, updating or upserting a Workspace
 * (spec schema `Workspace`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('Workspace')]
final readonly class WorkspaceInput implements Input
{
    /**
     * @param string|null          $name      A unique string representing a UTF-8 encoded name. Required by the spec on create.
     * @param string|null          $comment   A description or additional information about the workspace.
     * @param WorkspaceConfig|null $config
     * @param int|null             $createdAt Unix epoch when the resource was created.
     * @param string|null          $id        A string representing a UUID (universally unique identifier).
     * @param WorkspaceMeta|null   $meta
     * @param int|null             $updatedAt Unix epoch when the resource was last updated.
     */
    public function __construct(
        public ?string $name = null,
        public ?string $comment = null,
        public ?WorkspaceConfig $config = null,
        public ?int $createdAt = null,
        public ?string $id = null,
        public ?WorkspaceMeta $meta = null,
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
            'comment' => $this->comment,
            'config' => $this->config?->toArray(),
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'meta' => $this->meta?->toArray(),
            'updated_at' => $this->updatedAt,
        ]);
    }
}
