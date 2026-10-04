<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Statsd;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.queue.concurrency_limit` in the StatsD Plugin doc.
 *
 * The number of of queue delivery timers.
 */
#[PluginSchema('Monitoring/statsd.md', '#/properties/config/properties/queue/properties/concurrency_limit')]
enum StatsdQueueConcurrencyLimit: int
{
    case ValueMinus1 = -1;
    case Value1 = 1;
}
