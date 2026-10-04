<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Datadog;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.metrics[].name` in the Datadog Plugin doc.
 *
 * Datadog metric’s name
 */
#[PluginSchema('Monitoring/datadog.md', '#/properties/config/properties/metrics/items/properties/name')]
enum MetricsName: string
{
    case KongLatency = 'kong_latency';
    case Latency = 'latency';
    case RequestCount = 'request_count';
    case RequestSize = 'request_size';
    case ResponseSize = 'response_size';
    case UpstreamLatency = 'upstream_latency';
}
