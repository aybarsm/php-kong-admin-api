<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Statsd;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.metrics[].consumer_identifier` in the StatsD Plugin doc.
 *
 * Authenticated user detail.
 */
#[PluginSchema('Monitoring/statsd.md', '#/properties/config/properties/metrics/items/properties/consumer_identifier')]
enum StatsdMetricsConsumerIdentifier: string
{
    case ConsumerId = 'consumer_id';
    case CustomId = 'custom_id';
    case Username = 'username';
}
