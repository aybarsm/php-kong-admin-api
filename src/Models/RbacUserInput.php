<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;
use SensitiveParameter;

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
    /** Properties the spec marks `x-encrypted` (plus reviewed secrets, spec-notes Q18); redacted in __debugInfo(). */
    private const array ENCRYPTED = ['userToken'];

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
        #[SensitiveParameter]
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

    /**
     * Redacts `x-encrypted` values.
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        $values = get_object_vars($this);
        foreach (self::ENCRYPTED as $property) {
            if ($values[$property] !== null) {
                $values[$property] = '***';
            }
        }

        return $values;
    }
}
