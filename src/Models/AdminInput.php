<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * Request body for creating, updating or upserting an Admin
 * (spec schema `Admin`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('Admin')]
final readonly class AdminInput implements Input
{
    /**
     * @param string|null            $username         The admin's username. Required by the spec on create.
     * @param ForeignKey|string|null $consumer         The consumer.
     * @param int|null               $createdAt        Unix epoch when the resource was created.
     * @param string|null            $customId         The Admin’s custom ID.
     * @param string|null            $email
     * @param string|null            $id               A string representing a UUID (universally unique identifier).
     * @param bool|null              $rbacTokenEnabled Allows the Admin to use and reset their RBAC token; true by default.
     * @param ForeignKey|string|null $rbacUser         The rbac user Id.
     * @param int|null               $status
     * @param int|null               $updatedAt        Unix epoch when the resource was last updated.
     * @param string|null            $usernameLower    The admin's username in lowercase.
     */
    public function __construct(
        public ?string $username = null,
        public ForeignKey|string|null $consumer = null,
        public ?int $createdAt = null,
        public ?string $customId = null,
        public ?string $email = null,
        public ?string $id = null,
        public ?bool $rbacTokenEnabled = null,
        public ForeignKey|string|null $rbacUser = null,
        public ?int $status = null,
        public ?int $updatedAt = null,
        public ?string $usernameLower = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'username' => $this->username,
            'consumer' => $this->consumer === null ? null : ForeignKey::of($this->consumer)->toArray(),
            'created_at' => $this->createdAt,
            'custom_id' => $this->customId,
            'email' => $this->email,
            'id' => $this->id,
            'rbac_token_enabled' => $this->rbacTokenEnabled,
            'rbac_user' => $this->rbacUser === null ? null : ForeignKey::of($this->rbacUser)->toArray(),
            'status' => $this->status,
            'updated_at' => $this->updatedAt,
            'username_lower' => $this->usernameLower,
        ]);
    }
}
