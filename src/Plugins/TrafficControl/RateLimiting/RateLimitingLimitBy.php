<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\RateLimiting;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.limit_by` in the Rate Limiting Plugin doc.
 *
 * The entity that is used when aggregating the limits.
 */
#[PluginSchema('TrafficControl/rate-limiting.md', '#/properties/config/properties/limit_by')]
enum RateLimitingLimitBy: string
{
    case Consumer = 'consumer';
    case ConsumerGroup = 'consumer-group';
    case Credential = 'credential';
    case Header = 'header';
    case Ip = 'ip';
    case Path = 'path';
    case Principal = 'principal';
    case Service = 'service';
}
