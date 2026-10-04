<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * An RBAC Role endpoint permission as returned by the Admin API (spec schema `RBACRoleEndpoint`).
 */
#[Schema('RBACRoleEndpoint')]
final readonly class RbacRoleEndpoint implements Model
{
    /**
     * @param list<string>    $actions
     * @param string          $endpoint  The endpoint associated with the RBAC role.
     * @param string|null     $comment   Additional comment or description for the RBAC role endpoint.
     * @param int|null        $createdAt Unix epoch when the resource was created.
     * @param bool|null       $negative  Indicates whether the RBAC role has negative permissions for the endpoint.
     * @param ForeignKey|null $role      The RBAC role associated with the endpoint.
     * @param int|null        $updatedAt Unix epoch when the resource was last updated.
     * @param string|null     $workspace The workspace associated with the endpoint.
     */
    public function __construct(
        public array $actions,
        public string $endpoint,
        public ?string $comment = null,
        public ?int $createdAt = null,
        public ?bool $negative = null,
        public ?ForeignKey $role = null,
        public ?int $updatedAt = null,
        public ?string $workspace = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $role = Data::mapOrNull($data, 'role');

        return new self(
            actions: Data::stringList($data, 'actions'),
            endpoint: Data::string($data, 'endpoint'),
            comment: Data::stringOrNull($data, 'comment'),
            createdAt: Data::intOrNull($data, 'created_at'),
            negative: Data::boolOrNull($data, 'negative'),
            role: $role === null ? null : ForeignKey::fromArray($role),
            updatedAt: Data::intOrNull($data, 'updated_at'),
            workspace: Data::stringOrNull($data, 'workspace'),
        );
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
            'role' => $this->role?->toArray(),
            'updated_at' => $this->updatedAt,
            'workspace' => $this->workspace,
        ]);
    }
}
