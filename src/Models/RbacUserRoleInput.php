<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Enums\RbacRoleSource;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * Request body for creating, updating or upserting an RBAC user-role assignment
 * (spec schema `RBACUserRole`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('RBACUserRole')]
final readonly class RbacUserRoleInput implements Input
{
    /**
     * @param ForeignKey|string|null $role       The RBAC role assigned to the user.
     * @param RbacRoleSource|null    $roleSource The origin of the RBAC user role.
     * @param ForeignKey|string|null $user       The RBAC user associated with the role.
     */
    public function __construct(
        public ForeignKey|string|null $role = null,
        public ?RbacRoleSource $roleSource = null,
        public ForeignKey|string|null $user = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'role' => $this->role === null ? null : ForeignKey::of($this->role)->toArray(),
            'role_source' => $this->roleSource?->value,
            'user' => $this->user === null ? null : ForeignKey::of($this->user)->toArray(),
        ]);
    }
}
