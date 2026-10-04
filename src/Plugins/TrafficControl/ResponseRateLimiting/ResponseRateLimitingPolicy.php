<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\ResponseRateLimiting;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.policy` in the Response Rate Limiting Plugin doc.
 *
 * The rate-limiting policies to use for retrieving and incrementing the limits.
 */
#[PluginSchema('TrafficControl/response-rate-limiting.md', '#/properties/config/properties/policy')]
enum ResponseRateLimitingPolicy: string
{
    case Cluster = 'cluster';
    case Local = 'local';
    case Redis = 'redis';
}
