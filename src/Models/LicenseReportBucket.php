<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `counters.buckets[]` object of LicenseReport (spec `#/components/responses/ReportResponse/content/application~1json/schema.counters.buckets[]`).
 */
#[Schema('#/components/responses/ReportResponse/content/application~1json/schema/properties/counters/properties/buckets')]
final readonly class LicenseReportBucket implements Model
{
    /**
     * @param string|null $bucket       Year and month when the requests were processed.
     * @param int|null    $requestCount Number of requests processed in the given month and year.
     */
    public function __construct(
        public ?string $bucket = null,
        public ?int $requestCount = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            bucket: Data::stringOrNull($data, 'bucket'),
            requestCount: Data::intOrNull($data, 'request_count'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'bucket' => $this->bucket,
            'request_count' => $this->requestCount,
        ]);
    }
}
