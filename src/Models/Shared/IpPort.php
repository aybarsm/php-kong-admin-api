<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models\Shared;

use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * An IP/CIDR and port pair matched by stream routes (spec `RouteJson.sources[]` and `RouteJson.destinations[]`).
 */
final readonly class IpPort implements Model
{
    /**
     * @param string|null $ip   an IP address or CIDR block, e.g. `192.168.0.0/16`
     * @param int|null    $port a port number between 0 and 65535
     */
    public function __construct(
        public ?string $ip = null,
        public ?int $port = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            ip: Data::stringOrNull($data, 'ip'),
            port: Data::intOrNull($data, 'port'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'ip' => $this->ip,
            'port' => $this->port,
        ]);
    }
}
