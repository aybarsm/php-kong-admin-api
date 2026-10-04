<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Request body for `POST /admins/password_resets` (spec request body `AdminPasswordResetRequest`).
 */
#[Schema('#/components/requestBodies/AdminPasswordResetRequest/content/application~1json/schema')]
final readonly class AdminPasswordResetRequestInput implements Input
{
    /**
     * @param string|null $email The registered admin's email.
     */
    public function __construct(
        public ?string $email = null,
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
        ]);
    }
}
