<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;
use SensitiveParameter;

/**
 * Request body for `POST /admins/register` (spec request body `AdminCredentialRegistrationRequest`).
 */
#[Schema('#/components/requestBodies/AdminCredentialRegistrationRequest/content/application~1json/schema')]
final readonly class AdminRegistrationInput implements Input
{
    /** Properties the spec marks `x-encrypted` (plus reviewed secrets, spec-notes Q18); redacted in __debugInfo(). */
    private const array ENCRYPTED = ['password', 'token'];

    /**
     * @param string|null $email
     * @param string|null $password
     * @param string|null $token
     * @param string|null $username
     */
    public function __construct(
        public ?string $email = null,
        #[SensitiveParameter]
        public ?string $password = null,
        #[SensitiveParameter]
        public ?string $token = null,
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
            'email' => $this->email,
            'password' => $this->password,
            'token' => $this->token,
            'username' => $this->username,
        ]);
    }

    /**
     * Redacts `x-encrypted` values.
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        $values = get_object_vars($this);
        foreach (self::ENCRYPTED as $property) {
            if ($values[$property] !== null) {
                $values[$property] = '***';
            }
        }

        return $values;
    }
}
