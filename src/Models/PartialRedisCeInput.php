<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Request body for creating, updating or upserting a `redis-ce` Partial
 * (spec schema `PartialRedisCe`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('PartialRedisCe')]
final readonly class PartialRedisCeInput implements Input
{
    /**
     * @param array<array-key, mixed>|null $config    Required by the spec on create.
     * @param string|null                  $type      Required by the spec on create.
     * @param int|null                     $createdAt Unix epoch when the resource was created.
     * @param string|null                  $id        A string representing a UUID (universally unique identifier).
     * @param string|null                  $name      A unique string representing a UTF-8 encoded name.
     * @param list<string>|null            $tags      A set of strings representing tags.
     * @param int|null                     $updatedAt Unix epoch when the resource was last updated.
     */
    public function __construct(
        public ?array $config = null,
        public ?string $type = 'redis-ce',
        public ?int $createdAt = null,
        public ?string $id = null,
        public ?string $name = null,
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
            'config' => $this->config,
            'type' => $this->type,
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'name' => $this->name,
            'tags' => $this->tags,
            'updated_at' => $this->updatedAt,
        ]);
    }
}
