<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Request body for creating, updating or upserting a Group
 * (spec schema `Group`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('Group')]
final readonly class GroupInput implements Input
{
    /**
     * @param string|null $name      The name of the group Required by the spec on create.
     * @param string|null $comment   Any comments associated with the specific group.
     * @param int|null    $createdAt Unix epoch when the resource was created.
     * @param string|null $id        A string representing a UUID (universally unique identifier).
     * @param int|null    $updatedAt Unix epoch when the resource was last updated.
     */
    public function __construct(
        public ?string $name = null,
        public ?string $comment = null,
        public ?int $createdAt = null,
        public ?string $id = null,
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
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'updated_at' => $this->updatedAt,
        ]);
    }
}
