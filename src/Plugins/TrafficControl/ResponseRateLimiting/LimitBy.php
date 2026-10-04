<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\ResponseRateLimiting;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.limit_by` in the Response Rate Limiting Plugin doc.
 *
 * The entity that will be used when aggregating the limits: `consumer`, `credential`, `ip`.
 */
#[PluginSchema('TrafficControl/response-rate-limiting.md', '#/properties/config/properties/limit_by')]
enum LimitBy: string
{
    case Consumer = 'consumer';
    case Credential = 'credential';
    case Ip = 'ip';
}
