<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `jwks` object of OidcJwk (spec `OidcJwk.jwks`).
 */
#[Schema('#/components/schemas/OidcJwk/properties/jwks')]
final readonly class OidcJwkSet implements Model
{
    /**
     * @param list<OidcJwkKey> $keys
     */
    public function __construct(
        public array $keys,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            keys: array_map(OidcJwkKey::fromArray(...), Data::listOfMaps($data, 'keys')),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'keys' => Data::toArrays($this->keys),
        ]);
    }
}
