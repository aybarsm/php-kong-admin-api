<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * A database audit record from `GET /audit/objects` (spec response `DatabaseAuditLogResponse`).
 */
#[Schema('#/components/responses/DatabaseAuditLogResponse/content/application~1json/schema')]
final readonly class AuditObject implements Model
{
    /**
     * @param array<array-key, mixed>|null $changes   Details of the database changes.
     * @param string|null                  $id        Unique identifier for the database audit log.
     * @param string|null                  $timestamp Timestamp of the database log.
     */
    public function __construct(
        public ?array $changes = null,
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
            changes: Data::freeFormOrNull($data, 'changes'),
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
            'changes' => $this->changes,
            'id' => $this->id,
            'timestamp' => $this->timestamp,
        ]);
    }
}
