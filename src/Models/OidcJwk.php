<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * An OpenID Connect JWK set as returned by the Admin API (spec schema `OidcJwk`).
 */
#[Schema('OidcJwk')]
final readonly class OidcJwk implements Model
{
    /**
     * @param OidcJwkSet  $jwks
     * @param string|null $id
     */
    public function __construct(
        public OidcJwkSet $jwks,
        public ?string $id = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            jwks: OidcJwkSet::fromArray(Data::map($data, 'jwks')),
            id: Data::stringOrNull($data, 'id'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'jwks' => $this->jwks->toArray(),
            'id' => $this->id,
        ]);
    }
}
