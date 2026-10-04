<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Enums\RbacRoleSource;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * An RBAC user-role assignment as returned by the Admin API (spec schema `RBACUserRole`).
 */
#[Schema('RBACUserRole')]
final readonly class RbacUserRole implements Model
{
    /**
     * @param ForeignKey|null     $role       The RBAC role assigned to the user.
     * @param RbacRoleSource|null $roleSource The origin of the RBAC user role.
     * @param ForeignKey|null     $user       The RBAC user associated with the role.
     */
    public function __construct(
        public ?ForeignKey $role = null,
        public ?RbacRoleSource $roleSource = null,
        public ?ForeignKey $user = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $role = Data::mapOrNull($data, 'role');
        $user = Data::mapOrNull($data, 'user');

        return new self(
            role: $role === null ? null : ForeignKey::fromArray($role),
            roleSource: Data::enumOrNull($data, 'role_source', RbacRoleSource::class),
            user: $user === null ? null : ForeignKey::fromArray($user),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'role' => $this->role?->toArray(),
            'role_source' => $this->roleSource?->value,
            'user' => $this->user?->toArray(),
        ]);
    }
}
