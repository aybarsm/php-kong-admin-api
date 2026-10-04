<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The keyring state from `GET /keyring` (spec response `KeyRingResponse`).
 */
#[Schema('#/components/responses/KeyRingResponse/content/application~1json/schema')]
final readonly class KeyringStatus implements Model
{
    /**
     * @param string|null       $active The ID of the active key.
     * @param list<string>|null $ids    The list of the active key IDs
     */
    public function __construct(
        public ?string $active = null,
        public ?array $ids = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            active: Data::stringOrNull($data, 'active'),
            ids: Data::stringListOrNull($data, 'ids'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'active' => $this->active,
            'ids' => $this->ids,
        ]);
    }
}
