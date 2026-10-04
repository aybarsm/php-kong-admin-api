<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Request body for `POST /admins` (spec request body `AdminCreationRequest`).
 */
#[Schema('#/components/requestBodies/AdminCreationRequest/content/application~1json/schema')]
final readonly class AdminCreationInput implements Input
{
    /**
     * @param string|null $customId         The admin's custom ID
     * @param string|null $email            The admin's email address.
     * @param bool|null   $rbacTokenEnabled Allows the admin to use and reset their RBAC token.
     * @param string|null $username         The admin's username
     */
    public function __construct(
        public ?string $customId = null,
        public ?string $email = null,
        public ?bool $rbacTokenEnabled = null,
        public ?string $username = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'custom_id' => $this->customId,
            'email' => $this->email,
            'rbac_token_enabled' => $this->rbacTokenEnabled,
            'username' => $this->username,
        ]);
    }
}
