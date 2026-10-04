<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * An Admin as returned by the Admin API (spec schema `Admin`).
 */
#[Schema('Admin')]
final readonly class Admin implements Model
{
    /**
     * @param string          $username         The admin's username.
     * @param ForeignKey|null $consumer         The consumer.
     * @param int|null        $createdAt        Unix epoch when the resource was created.
     * @param string|null     $customId         The Admin’s custom ID.
     * @param string|null     $email
     * @param string|null     $id               A string representing a UUID (universally unique identifier).
     * @param bool|null       $rbacTokenEnabled Allows the Admin to use and reset their RBAC token; true by default.
     * @param ForeignKey|null $rbacUser         The rbac user Id.
     * @param int|null        $status
     * @param int|null        $updatedAt        Unix epoch when the resource was last updated.
     * @param string|null     $usernameLower    The admin's username in lowercase.
     */
    public function __construct(
        public string $username,
        public ?ForeignKey $consumer = null,
        public ?int $createdAt = null,
        public ?string $customId = null,
        public ?string $email = null,
        public ?string $id = null,
        public ?bool $rbacTokenEnabled = null,
        public ?ForeignKey $rbacUser = null,
        public ?int $status = null,
        public ?int $updatedAt = null,
        public ?string $usernameLower = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $consumer = Data::mapOrNull($data, 'consumer');
        $rbacUser = Data::mapOrNull($data, 'rbac_user');

        return new self(
            username: Data::string($data, 'username'),
            consumer: $consumer === null ? null : ForeignKey::fromArray($consumer),
            createdAt: Data::intOrNull($data, 'created_at'),
            customId: Data::stringOrNull($data, 'custom_id'),
            email: Data::stringOrNull($data, 'email'),
            id: Data::stringOrNull($data, 'id'),
            rbacTokenEnabled: Data::boolOrNull($data, 'rbac_token_enabled'),
            rbacUser: $rbacUser === null ? null : ForeignKey::fromArray($rbacUser),
            status: Data::intOrNull($data, 'status'),
            updatedAt: Data::intOrNull($data, 'updated_at'),
            usernameLower: Data::stringOrNull($data, 'username_lower'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'username' => $this->username,
            'consumer' => $this->consumer?->toArray(),
            'created_at' => $this->createdAt,
            'custom_id' => $this->customId,
            'email' => $this->email,
            'id' => $this->id,
            'rbac_token_enabled' => $this->rbacTokenEnabled,
            'rbac_user' => $this->rbacUser?->toArray(),
            'status' => $this->status,
            'updated_at' => $this->updatedAt,
            'username_lower' => $this->usernameLower,
        ]);
    }
}
