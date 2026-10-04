<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Zipkin;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.queue.concurrency_limit` in the Zipkin Plugin doc.
 *
 * The number of of queue delivery timers.
 */
#[PluginSchema('Monitoring/zipkin.md', '#/properties/config/properties/queue/properties/concurrency_limit')]
enum ZipkinQueueConcurrencyLimit: int
{
    case ValueMinus1 = -1;
    case Value1 = 1;
}
