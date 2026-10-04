<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * A request audit record from `GET /audit/requests` (spec response `ListAuditObjectsResponse`).
 */
#[Schema('#/components/responses/ListAuditObjectsResponse/content/application~1json/schema')]
final readonly class AuditRequest implements Model
{
    /**
     * @param array<array-key, mixed>|null $details   Additional log details.
     * @param string|null                  $id        Unique identifier for the audit log.
     * @param string|null                  $timestamp Timestamp of the log.
     */
    public function __construct(
        public ?array $details = null,
        public ?string $id = null,
        public ?string $timestamp = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            details: Data::freeFormOrNull($data, 'details'),
            id: Data::stringOrNull($data, 'id'),
            timestamp: Data::stringOrNull($data, 'timestamp'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'details' => $this->details,
            'id' => $this->id,
            'timestamp' => $this->timestamp,
        ]);
    }
}
