<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The roles assigned to an Admin (spec response `AdminRolesCreated`).
 */
#[Schema('#/components/responses/AdminRolesCreated/content/application~1json/schema')]
final readonly class AdminRoles implements Model
{
    /**
     * @param list<AdminRole>|null $roles
     */
    public function __construct(
        public ?array $roles = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $roles = Data::listOfMapsOrNull($data, 'roles');

        return new self(
            roles: $roles === null ? null : array_map(AdminRole::fromArray(...), $roles),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'roles' => Data::toArrays($this->roles),
        ]);
    }
}
