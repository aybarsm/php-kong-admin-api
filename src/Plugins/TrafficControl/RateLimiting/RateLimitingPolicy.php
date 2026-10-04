<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\RateLimiting;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.policy` in the Rate Limiting Plugin doc.
 *
 * The rate-limiting policies to use for retrieving and incrementing the limits.
 */
#[PluginSchema('TrafficControl/rate-limiting.md', '#/properties/config/properties/policy')]
enum RateLimitingPolicy: string
{
    case Cluster = 'cluster';
    case Local = 'local';
    case Redis = 'redis';
}
