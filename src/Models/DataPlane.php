<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * A connected data plane from `GET /clustering/data-planes` (spec response `GetConnectedDataPlanesListResponse`).
 */
#[Schema('#/components/responses/GetConnectedDataPlanesListResponse/content/application~1json/schema/properties/data')]
final readonly class DataPlane implements Model
{
    /**
     * @param DataPlaneCertDetails|null $certDetails
     * @param string|null               $configHash  The hash of the current configuration on the data plane.
     * @param string|null               $hostname    The hostname of the data plane.
     * @param string|null               $id          Unique identifier of the data plane.
     * @param string|null               $ip          The IP address of the data plane.
     * @param DataPlaneLabels|null      $labels      Metadata labels attached to the data plane.
     * @param int|null                  $lastSeen    Unix timestamp when the data plane was last seen by the control plane.
     * @param string|null               $syncStatus  The sync status of the data plane.
     * @param int|null                  $ttl         Time-to-live for the connection.
     * @param int|null                  $updatedAt   Unix timestamp of the last update.
     * @param string|null               $version     The version of Kong running on the data plane.
     */
    public function __construct(
        public ?DataPlaneCertDetails $certDetails = null,
        public ?string $configHash = null,
        public ?string $hostname = null,
        public ?string $id = null,
        public ?string $ip = null,
        public ?DataPlaneLabels $labels = null,
        public ?int $lastSeen = null,
        public ?string $syncStatus = null,
        public ?int $ttl = null,
        public ?int $updatedAt = null,
        public ?string $version = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $certDetails = Data::mapOrNull($data, 'cert_details');
        $labels = Data::mapOrNull($data, 'labels');

        return new self(
            certDetails: $certDetails === null ? null : DataPlaneCertDetails::fromArray($certDetails),
            configHash: Data::stringOrNull($data, 'config_hash'),
            hostname: Data::stringOrNull($data, 'hostname'),
            id: Data::stringOrNull($data, 'id'),
            ip: Data::stringOrNull($data, 'ip'),
            labels: $labels === null ? null : DataPlaneLabels::fromArray($labels),
            lastSeen: Data::intOrNull($data, 'last_seen'),
            syncStatus: Data::stringOrNull($data, 'sync_status'),
            ttl: Data::intOrNull($data, 'ttl'),
            updatedAt: Data::intOrNull($data, 'updated_at'),
            version: Data::stringOrNull($data, 'version'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'cert_details' => $this->certDetails?->toArray(),
            'config_hash' => $this->configHash,
            'hostname' => $this->hostname,
            'id' => $this->id,
            'ip' => $this->ip,
            'labels' => $this->labels?->toArray(),
            'last_seen' => $this->lastSeen,
            'sync_status' => $this->syncStatus,
            'ttl' => $this->ttl,
            'updated_at' => $this->updatedAt,
            'version' => $this->version,
        ]);
    }
}
