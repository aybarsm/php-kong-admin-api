<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Request body for `PATCH /admins/password_resets` (spec request body `AdminPasswordResetConfirmationRequest`).
 */
#[Schema('#/components/requestBodies/AdminPasswordResetConfirmationRequest/content/application~1json/schema')]
final readonly class AdminPasswordResetInput implements Input
{
    /**
     * @param string|null $email
     * @param string|null $password
     * @param string|null $token
     */
    public function __construct(
        public ?string $email = null,
        public ?string $password = null,
        public ?string $token = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'email' => $this->email,
            'password' => $this->password,
            'token' => $this->token,
        ]);
    }
}
