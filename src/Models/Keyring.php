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
}
