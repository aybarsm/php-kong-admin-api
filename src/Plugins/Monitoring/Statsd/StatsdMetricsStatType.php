<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Statsd;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.metrics[].stat_type` in the StatsD Plugin doc.
 *
 * Determines what sort of event a metric represents.
 */
#[PluginSchema('Monitoring/statsd.md', '#/properties/config/properties/metrics/items/properties/stat_type')]
enum StatsdMetricsStatType: string
{
    case Counter = 'counter';
    case Gauge = 'gauge';
    case Histogram = 'histogram';
    case Meter = 'meter';
    case Set = 'set';
    case Timer = 'timer';
}
