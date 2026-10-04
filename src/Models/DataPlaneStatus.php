<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The status of one data plane from `GET /clustering/status` (values of spec response
 * `GetConnectedDataPlaneStatusResponse`, keyed by node ID).
 */
#[Schema('#/components/responses/GetConnectedDataPlaneStatusResponse/content/application~1json/schema/additionalProperties')]
final readonly class DataPlaneStatus implements Model
{
    /**
     * @param string|null $configHash Hash of the configuration running on the data plane.
     * @param string|null $hostname   Hostname of the data plane.
     * @param string|null $ip         The IP address of the data plane.
     * @param int|null    $lastSeen   Unix timestamp of the last interaction between the data plane and control plane.
     */
    public function __construct(
        public ?string $configHash = null,
        public ?string $hostname = null,
        public ?string $ip = null,
        public ?int $lastSeen = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            configHash: Data::stringOrNull($data, 'config_hash'),
            hostname: Data::stringOrNull($data, 'hostname'),
            ip: Data::stringOrNull($data, 'ip'),
            lastSeen: Data::intOrNull($data, 'last_seen'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'config_hash' => $this->configHash,
            'hostname' => $this->hostname,
            'ip' => $this->ip,
            'last_seen' => $this->lastSeen,
        ]);
    }
}
