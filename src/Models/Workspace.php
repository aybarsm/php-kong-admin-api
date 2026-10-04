<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * A Workspace as returned by the Admin API (spec schema `Workspace`).
 */
#[Schema('Workspace')]
final readonly class Workspace implements Model
{
    /**
     * @param string               $name      A unique string representing a UTF-8 encoded name.
     * @param string|null          $comment   A description or additional information about the workspace.
     * @param WorkspaceConfig|null $config
     * @param int|null             $createdAt Unix epoch when the resource was created.
     * @param string|null          $id        A string representing a UUID (universally unique identifier).
     * @param WorkspaceMeta|null   $meta
     * @param int|null             $updatedAt Unix epoch when the resource was last updated.
     */
    public function __construct(
        public string $name,
        public ?string $comment = null,
        public ?WorkspaceConfig $config = null,
        public ?int $createdAt = null,
        public ?string $id = null,
        public ?WorkspaceMeta $meta = null,
        public ?int $updatedAt = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $config = Data::mapOrNull($data, 'config');
        $meta = Data::mapOrNull($data, 'meta');

        return new self(
            name: Data::string($data, 'name'),
            comment: Data::stringOrNull($data, 'comment'),
            config: $config === null ? null : WorkspaceConfig::fromArray($config),
            createdAt: Data::intOrNull($data, 'created_at'),
            id: Data::stringOrNull($data, 'id'),
            meta: $meta === null ? null : WorkspaceMeta::fromArray($meta),
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
            'config' => $this->config?->toArray(),
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'meta' => $this->meta?->toArray(),
            'updated_at' => $this->updatedAt,
        ]);
    }
}
