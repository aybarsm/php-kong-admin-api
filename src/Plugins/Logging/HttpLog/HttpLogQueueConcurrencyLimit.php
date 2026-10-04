<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Logging\HttpLog;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.queue.concurrency_limit` in the HTTP Log Plugin doc.
 *
 * The number of of queue delivery timers.
 */
#[PluginSchema('Logging/http-log.md', '#/properties/config/properties/queue/properties/concurrency_limit')]
enum HttpLogQueueConcurrencyLimit: int
{
    case ValueMinus1 = -1;
    case Value1 = 1;
}
