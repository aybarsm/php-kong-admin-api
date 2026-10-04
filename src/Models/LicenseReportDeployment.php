<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Enums\DeploymentType;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `deployment_info` object of LicenseReport (spec `#/components/responses/ReportResponse/content/application~1json/schema.deployment_info`).
 */
#[Schema('#/components/responses/ReportResponse/content/application~1json/schema/properties/deployment_info')]
final readonly class LicenseReportDeployment implements Model
{
    /**
     * @param int|null            $connectedDpCount Number of data planes currently connected to the control plane.
     * @param DeploymentType|null $type             The deployment topology type.
     */
    public function __construct(
        public ?int $connectedDpCount = null,
        public ?DeploymentType $type = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            connectedDpCount: Data::intOrNull($data, 'connected_dp_count'),
            type: Data::enumOrNull($data, 'type', DeploymentType::class),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'connected_dp_count' => $this->connectedDpCount,
            'type' => $this->type?->value,
        ]);
    }
}
