<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\AccessControlEnforcement;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.rate_limiting.redis.sentinel_role` in the Access Control Enforcement Plugin doc.
 *
 * Sentinel role to use for Redis connections when the `redis` strategy is defined.
 */
#[PluginSchema('TrafficControl/access-control-enforcement.md', '#/properties/config/properties/rate_limiting/properties/redis/properties/sentinel_role')]
enum RateLimitingRedisSentinelRole: string
{
    case Any = 'any';
    case Master = 'master';
    case Slave = 'slave';
}
