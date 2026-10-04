<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * A License as returned by the Admin API (spec response `LicenseResponse`).
 */
#[Schema('#/components/responses/LicenseResponse/content/application~1json/schema')]
final readonly class License implements Model
{
    /**
     * @param int|null    $createdAt
     * @param string|null $id        The UUID of the license
     * @param string|null $payload   The Kong Gateway license in JSON format.
     * @param int|null    $updatedAt
     */
    public function __construct(
        public ?int $createdAt = null,
        public ?string $id = null,
        public ?string $payload = null,
        public ?int $updatedAt = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            createdAt: Data::intOrNull($data, 'created_at'),
            id: Data::stringOrNull($data, 'id'),
            payload: Data::stringOrNull($data, 'payload'),
            updatedAt: Data::intOrNull($data, 'updated_at'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'payload' => $this->payload,
            'updated_at' => $this->updatedAt,
        ]);
    }
}
