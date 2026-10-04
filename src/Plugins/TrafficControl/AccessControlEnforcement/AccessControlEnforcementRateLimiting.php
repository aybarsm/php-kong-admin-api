<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\AccessControlEnforcement;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.rate_limiting` object of the Access Control Enforcement plugin (doc `TrafficControl/access-control-enforcement.md`).
 */
#[PluginSchema('TrafficControl/access-control-enforcement.md', '#/properties/config/properties/rate_limiting')]
final readonly class AccessControlEnforcementRateLimiting implements Model
{
    /**
     * @param AccessControlEnforcementRateLimitingRedis|null $redis
     * @param float|null                                     $syncRate How often to sync counter data to the central data store.
     */
    public function __construct(
        public ?AccessControlEnforcementRateLimitingRedis $redis = null,
        public ?float $syncRate = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $redis = Data::mapOrNull($data, 'redis');

        return new self(
            redis: $redis === null ? null : AccessControlEnforcementRateLimitingRedis::fromArray($redis),
            syncRate: Data::floatOrNull($data, 'sync_rate'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'redis' => $this->redis?->toArray(),
            'sync_rate' => $this->syncRate,
        ]);
    }
}
