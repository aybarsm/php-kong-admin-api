<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * An RBAC User as returned by the Admin API (spec schema `RBACUser`).
 *
 * The write-only `user_token` is available on RbacUserInput only.
 */
#[Schema('RBACUser')]
final readonly class RbacUser implements Model
{
    /**
     * @param string      $name           The name of the user.
     * @param string|null $comment        Any comments associated with the user.
     * @param int|null    $createdAt      Unix epoch when the resource was created.
     * @param bool|null   $enabled        Wether or not the user has RBAC enabled.
     * @param string|null $id             A string representing a UUID (universally unique identifier).
     * @param int|null    $updatedAt      Unix epoch when the resource was last updated.
     * @param string|null $userTokenIdent The user token.
     */
    public function __construct(
        public string $name,
        public ?string $comment = null,
        public ?int $createdAt = null,
        public ?bool $enabled = null,
        public ?string $id = null,
        public ?int $updatedAt = null,
        public ?string $userTokenIdent = null,
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
            comment: Data::stringOrNull($data, 'comment'),
            createdAt: Data::intOrNull($data, 'created_at'),
            enabled: Data::boolOrNull($data, 'enabled'),
            id: Data::stringOrNull($data, 'id'),
            updatedAt: Data::intOrNull($data, 'updated_at'),
            userTokenIdent: Data::stringOrNull($data, 'user_token_ident'),
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
            'created_at' => $this->createdAt,
            'enabled' => $this->enabled,
            'id' => $this->id,
            'updated_at' => $this->updatedAt,
            'user_token_ident' => $this->userTokenIdent,
        ]);
    }
}
