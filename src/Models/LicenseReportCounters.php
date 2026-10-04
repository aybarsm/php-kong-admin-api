<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `counters` object of LicenseReport (spec `#/components/responses/ReportResponse/content/application~1json/schema.counters`).
 */
#[Schema('#/components/responses/ReportResponse/content/application~1json/schema/properties/counters')]
final readonly class LicenseReportCounters implements Model
{
    /**
     * @param list<LicenseReportBucket>|null $buckets       A list of year-month buckets and the number of requests made in each one.
     * @param float|null                     $totalRequests The total number of requests made in all buckets.
     */
    public function __construct(
        public ?array $buckets = null,
        public ?float $totalRequests = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $buckets = Data::listOfMapsOrNull($data, 'buckets');

        return new self(
            buckets: $buckets === null ? null : array_map(LicenseReportBucket::fromArray(...), $buckets),
            totalRequests: Data::floatOrNull($data, 'total_requests'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'buckets' => Data::toArrays($this->buckets),
            'total_requests' => $this->totalRequests,
        ]);
    }
}
