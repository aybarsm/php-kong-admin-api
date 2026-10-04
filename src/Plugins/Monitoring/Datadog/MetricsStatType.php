<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Datadog;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.metrics[].stat_type` in the Datadog Plugin doc.
 *
 * Determines what sort of event the metric represents
 */
#[PluginSchema('Monitoring/datadog.md', '#/properties/config/properties/metrics/items/properties/stat_type')]
enum MetricsStatType: string
{
    case Counter = 'counter';
    case Distribution = 'distribution';
    case Gauge = 'gauge';
    case Histogram = 'histogram';
    case Meter = 'meter';
    case Set = 'set';
    case Timer = 'timer';
}
