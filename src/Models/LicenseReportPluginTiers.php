<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `plugins_count.tiers` object of LicenseReport (spec `#/components/responses/ReportResponse/content/application~1json/schema.plugins_count.tiers`).
 */
#[Schema('#/components/responses/ReportResponse/content/application~1json/schema/properties/plugins_count/properties/tiers')]
final readonly class LicenseReportPluginTiers implements Model
{
    /**
     * @param array<array-key, mixed>|null $custom
     * @param array<array-key, mixed>|null $enterprise
     * @param array<array-key, mixed>|null $free
     */
    public function __construct(
        public ?array $custom = null,
        public ?array $enterprise = null,
        public ?array $free = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            custom: Data::freeFormOrNull($data, 'custom'),
            enterprise: Data::freeFormOrNull($data, 'enterprise'),
            free: Data::freeFormOrNull($data, 'free'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'custom' => $this->custom,
            'enterprise' => $this->enterprise,
            'free' => $this->free,
        ]);
    }
}
