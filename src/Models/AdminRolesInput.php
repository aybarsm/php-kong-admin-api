<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Request body for `POST /admins/{adminNameOrId}/roles` (spec request body `AdminRoleUpdateRequest`).
 */
#[Schema('#/components/requestBodies/AdminRoleUpdateRequest/content/application~1json/schema')]
final readonly class AdminRolesInput implements Input
{
    /**
     * @param string|null $roles
     */
    public function __construct(
        public ?string $roles = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'roles' => $this->roles,
        ]);
    }
}
