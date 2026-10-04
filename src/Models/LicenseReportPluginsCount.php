<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `plugins_count` object of LicenseReport (spec `#/components/responses/ReportResponse/content/application~1json/schema.plugins_count`).
 */
#[Schema('#/components/responses/ReportResponse/content/application~1json/schema/properties/plugins_count')]
final readonly class LicenseReportPluginsCount implements Model
{
    /**
     * @param LicenseReportPluginTiers|null $tiers              Active plugins grouped by tier (free, enterprise, custom).
     * @param int|null                      $uniqueRouteKafkas  Number of unique Kafka broker addresses (host:port) across Kafka plugins configured on service-less routes.
     * @param int|null                      $uniqueRouteLambdas Number of unique AWS Lambda function names across aws-lambda plugins configured on service-less routes.
     */
    public function __construct(
        public ?LicenseReportPluginTiers $tiers = null,
        public ?int $uniqueRouteKafkas = null,
        public ?int $uniqueRouteLambdas = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $tiers = Data::mapOrNull($data, 'tiers');

        return new self(
            tiers: $tiers === null ? null : LicenseReportPluginTiers::fromArray($tiers),
            uniqueRouteKafkas: Data::intOrNull($data, 'unique_route_kafkas'),
            uniqueRouteLambdas: Data::intOrNull($data, 'unique_route_lambdas'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'tiers' => $this->tiers?->toArray(),
            'unique_route_kafkas' => $this->uniqueRouteKafkas,
            'unique_route_lambdas' => $this->uniqueRouteLambdas,
        ]);
    }
}
