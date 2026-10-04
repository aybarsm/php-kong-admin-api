<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * A Consumer as returned by the Admin API (spec schema `Consumer`).
 */
#[Schema('Consumer')]
final readonly class Consumer implements Model
{
    /**
     * @param int|null          $createdAt Unix epoch when the resource was created.
     * @param string|null       $customId  Field for storing an existing unique ID for the Consumer - useful for mapping Kong with users in your existin…
     * @param string|null       $id        A string representing a UUID (universally unique identifier).
     * @param list<string>|null $tags      An optional set of strings associated with the Consumer for grouping and filtering.
     * @param int|null          $updatedAt Unix epoch when the resource was last updated.
     * @param string|null       $username  The unique username of the Consumer.
     */
    public function __construct(
        public ?int $createdAt = null,
        public ?string $customId = null,
        public ?string $id = null,
        public ?array $tags = null,
        public ?int $updatedAt = null,
        public ?string $username = null,
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
            customId: Data::stringOrNull($data, 'custom_id'),
            id: Data::stringOrNull($data, 'id'),
            tags: Data::stringListOrNull($data, 'tags'),
            updatedAt: Data::intOrNull($data, 'updated_at'),
            username: Data::stringOrNull($data, 'username'),
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
            'custom_id' => $this->customId,
            'id' => $this->id,
            'tags' => $this->tags,
            'updated_at' => $this->updatedAt,
            'username' => $this->username,
        ]);
    }
}
