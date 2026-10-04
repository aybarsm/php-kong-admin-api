<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * An RBAC user-group assignment as returned by the Admin API (spec schema `RBACUserGroup`).
 */
#[Schema('RBACUserGroup')]
final readonly class RbacUserGroup implements Model
{
    /**
     * @param ForeignKey|null $group The group assigned to the user.
     * @param ForeignKey|null $user  The RBAC user associated with the group.
     */
    public function __construct(
        public ?ForeignKey $group = null,
        public ?ForeignKey $user = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $group = Data::mapOrNull($data, 'group');
        $user = Data::mapOrNull($data, 'user');

        return new self(
            group: $group === null ? null : ForeignKey::fromArray($group),
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
            'group' => $this->group?->toArray(),
            'user' => $this->user?->toArray(),
        ]);
    }
}
