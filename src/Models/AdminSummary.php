<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * An Admin as listed by `GET /admins` (spec response `ListAdminsResponse`).
 *
 * `PATCH /admins/{adminNameOrId}/workspaces/{workspaceNameOrId}` returns the same properties inline.
 */
#[Schema('#/components/responses/ListAdminsResponse/content/application~1json/schema/properties/data')]
final readonly class AdminSummary implements Model
{
    /**
     * @param int|null    $createdAt
     * @param string|null $email
     * @param string|null $id
     * @param bool|null   $rbacTokenEnabled
     * @param int|null    $status           The status field indicates the state of the invitation.
     * @param int|null    $updatedAt
     * @param string|null $username
     */
    public function __construct(
        public ?int $createdAt = null,
        public ?string $email = null,
        public ?string $id = null,
        public ?bool $rbacTokenEnabled = null,
        public ?int $status = null,
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
            email: Data::stringOrNull($data, 'email'),
            id: Data::stringOrNull($data, 'id'),
            rbacTokenEnabled: Data::boolOrNull($data, 'rbac_token_enabled'),
            status: Data::intOrNull($data, 'status'),
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
            'email' => $this->email,
            'id' => $this->id,
            'rbac_token_enabled' => $this->rbacTokenEnabled,
            'status' => $this->status,
            'updated_at' => $this->updatedAt,
            'username' => $this->username,
        ]);
    }
}
