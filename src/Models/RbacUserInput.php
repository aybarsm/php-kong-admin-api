<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Request body for creating, updating or upserting an RBAC User
 * (spec schema `RBACUser`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('RBACUser')]
final readonly class RbacUserInput implements Input
{
    /**
     * @param string|null $name           The name of the user. Required by the spec on create.
     * @param string|null $userToken      Required by the spec on create.
     * @param string|null $comment        Any comments associated with the user.
     * @param int|null    $createdAt      Unix epoch when the resource was created.
     * @param bool|null   $enabled        Wether or not the user has RBAC enabled.
     * @param string|null $id             A string representing a UUID (universally unique identifier).
     * @param int|null    $updatedAt      Unix epoch when the resource was last updated.
     * @param string|null $userTokenIdent The user token.
     */
    public function __construct(
        public ?string $name = null,
        public ?string $userToken = null,
        public ?string $comment = null,
        public ?int $createdAt = null,
        public ?bool $enabled = null,
        public ?string $id = null,
        public ?int $updatedAt = null,
        public ?string $userTokenIdent = null,
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
            'user_token' => $this->userToken,
            'comment' => $this->comment,
            'created_at' => $this->createdAt,
            'enabled' => $this->enabled,
            'id' => $this->id,
            'updated_at' => $this->updatedAt,
            'user_token_ident' => $this->userTokenIdent,
        ]);
    }
}
