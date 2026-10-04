<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Statsd;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.metrics[].service_identifier` in the StatsD Plugin doc.
 *
 * Service detail.
 */
#[PluginSchema('Monitoring/statsd.md', '#/properties/config/properties/metrics/items/properties/service_identifier')]
enum MetricsServiceIdentifier: string
{
    case ServiceHost = 'service_host';
    case ServiceId = 'service_id';
    case ServiceName = 'service_name';
    case ServiceNameOrHost = 'service_name_or_host';
}
