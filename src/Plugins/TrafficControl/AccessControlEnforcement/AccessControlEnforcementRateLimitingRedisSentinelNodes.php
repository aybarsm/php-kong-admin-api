<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\AccessControlEnforcement;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.rate_limiting.redis.sentinel_nodes[]` object of the Access Control Enforcement plugin (doc `TrafficControl/access-control-enforcement.md`).
 */
#[PluginSchema('TrafficControl/access-control-enforcement.md', '#/properties/config/properties/rate_limiting/properties/redis/properties/sentinel_nodes/items')]
final readonly class AccessControlEnforcementRateLimitingRedisSentinelNodes implements Model
{
    /**
     * @param string|null $host A string representing a host name, such as example.com. Default: `127.0.0.1`.
     * @param int|null    $port An integer representing a port number between 0 and 65535, inclusive. Default: `6379`.
     */
    public function __construct(
        public ?string $host = null,
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
            host: Data::stringOrNull($data, 'host'),
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
            'host' => $this->host,
            'port' => $this->port,
        ]);
    }
}
