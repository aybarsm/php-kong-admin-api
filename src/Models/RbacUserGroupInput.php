<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * Request body for creating, updating or upserting an RBAC user-group assignment
 * (spec schema `RBACUserGroup`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('RBACUserGroup')]
final readonly class RbacUserGroupInput implements Input
{
    /**
     * @param ForeignKey|string|null $group The group assigned to the user.
     * @param ForeignKey|string|null $user  The RBAC user associated with the group.
     */
    public function __construct(
        public ForeignKey|string|null $group = null,
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
            'group' => $this->group === null ? null : ForeignKey::of($this->group)->toArray(),
            'user' => $this->user === null ? null : ForeignKey::of($this->user)->toArray(),
        ]);
    }
}
