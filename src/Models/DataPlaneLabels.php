<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `labels` object of DataPlane (spec `#/components/responses/GetConnectedDataPlanesListResponse/content/application~1json/schema/properties/data.labels`).
 */
#[Schema('#/components/responses/GetConnectedDataPlanesListResponse/content/application~1json/schema/properties/data/items/properties/labels')]
final readonly class DataPlaneLabels implements Model
{
    /**
     * @param string|null $deployment The deployment name.
     * @param string|null $region     The region of the data plane.
     */
    public function __construct(
        public ?string $deployment = null,
        public ?string $region = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            deployment: Data::stringOrNull($data, 'deployment'),
            region: Data::stringOrNull($data, 'region'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'deployment' => $this->deployment,
            'region' => $this->region,
        ]);
    }
}
