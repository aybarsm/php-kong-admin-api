<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Statsd;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.tag_style` in the StatsD Plugin doc.
 */
#[PluginSchema('Monitoring/statsd.md', '#/properties/config/properties/tag_style')]
enum TagStyle: string
{
    case Dogstatsd = 'dogstatsd';
    case Influxdb = 'influxdb';
    case Librato = 'librato';
    case Signalfx = 'signalfx';
}
