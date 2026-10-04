<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * Request body for creating, updating or upserting an RBAC Role endpoint permission
 * (spec schema `RBACRoleEndpoint`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('RBACRoleEndpoint')]
final readonly class RbacRoleEndpointInput implements Input
{
    /**
     * @param list<string>|null      $actions   Required by the spec on create.
     * @param string|null            $endpoint  The endpoint associated with the RBAC role. Required by the spec on create.
     * @param string|null            $comment   Additional comment or description for the RBAC role endpoint.
     * @param int|null               $createdAt Unix epoch when the resource was created.
     * @param bool|null              $negative  Indicates whether the RBAC role has negative permissions for the endpoint.
     * @param ForeignKey|string|null $role      The RBAC role associated with the endpoint.
     * @param int|null               $updatedAt Unix epoch when the resource was last updated.
     * @param string|null            $workspace The workspace associated with the endpoint.
     */
    public function __construct(
        public ?array $actions = null,
        public ?string $endpoint = null,
        public ?string $comment = null,
        public ?int $createdAt = null,
        public ?bool $negative = null,
        public ForeignKey|string|null $role = null,
        public ?int $updatedAt = null,
        public ?string $workspace = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'actions' => $this->actions,
            'endpoint' => $this->endpoint,
            'comment' => $this->comment,
            'created_at' => $this->createdAt,
            'negative' => $this->negative,
            'role' => $this->role === null ? null : ForeignKey::of($this->role)->toArray(),
            'updated_at' => $this->updatedAt,
            'workspace' => $this->workspace,
        ]);
    }
}
