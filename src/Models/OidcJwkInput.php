<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Request body for creating, updating or upserting an OpenID Connect JWK set
 * (spec schema `OidcJwk`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('OidcJwk')]
final readonly class OidcJwkInput implements Input
{
    /**
     * @param OidcJwkSet|null $jwks Required by the spec on create.
     * @param string|null     $id
     */
    public function __construct(
        public ?OidcJwkSet $jwks = null,
        public ?string $id = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'jwks' => $this->jwks?->toArray(),
            'id' => $this->id,
        ]);
    }
}
