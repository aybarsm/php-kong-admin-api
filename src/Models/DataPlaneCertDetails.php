<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `cert_details` object of DataPlane (spec `#/components/responses/GetConnectedDataPlanesListResponse/content/application~1json/schema/properties/data.cert_details`).
 */
#[Schema('#/components/responses/GetConnectedDataPlanesListResponse/content/application~1json/schema/properties/data/items/properties/cert_details')]
final readonly class DataPlaneCertDetails implements Model
{
    /**
     * @param int|null $expiryTimestamp Timestamp for when the certificate expires.
     */
    public function __construct(
        public ?int $expiryTimestamp = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            expiryTimestamp: Data::intOrNull($data, 'expiry_timestamp'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'expiry_timestamp' => $this->expiryTimestamp,
        ]);
    }
}
