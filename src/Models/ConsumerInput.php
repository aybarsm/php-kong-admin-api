<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Request body for creating, updating or upserting a Consumer
 * (spec schema `Consumer`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('Consumer')]
final readonly class ConsumerInput implements Input
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
