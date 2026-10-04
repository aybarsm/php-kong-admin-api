<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `pem` object of Key (spec `Key.pem`).
 */
#[Schema('#/components/schemas/Key/properties/pem')]
final readonly class KeyPem implements Model
{
    /** Properties the spec marks `x-encrypted` (plus reviewed secrets, spec-notes Q18); redacted in __debugInfo(). */
    private const array ENCRYPTED = ['privateKey'];

    /**
     * @param string|null $privateKey
     * @param string|null $publicKey
     */
    public function __construct(
        public ?string $privateKey = null,
        public ?string $publicKey = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            privateKey: Data::stringOrNull($data, 'private_key'),
            publicKey: Data::stringOrNull($data, 'public_key'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'private_key' => $this->privateKey,
            'public_key' => $this->publicKey,
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
