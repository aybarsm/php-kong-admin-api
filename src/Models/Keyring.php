<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * A keyring key (spec schema `Keyring`).
 */
#[Schema('Keyring')]
final readonly class Keyring implements Model
{
    /** Properties the spec marks `x-encrypted` (plus reviewed secrets, spec-notes Q18); redacted in __debugInfo(). */
    private const array ENCRYPTED = ['key'];

    /**
     * @param string|null $id  The ID of the key.
     * @param string|null $key The generated encryption key.
     */
    public function __construct(
        public ?string $id = null,
        public ?string $key = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            id: Data::stringOrNull($data, 'id'),
            key: Data::stringOrNull($data, 'key'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'id' => $this->id,
            'key' => $this->key,
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
