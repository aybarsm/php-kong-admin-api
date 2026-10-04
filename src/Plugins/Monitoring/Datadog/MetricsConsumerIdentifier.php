<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Datadog;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.metrics[].consumer_identifier` in the Datadog Plugin doc.
 *
 * Authenticated user detail
 */
#[PluginSchema('Monitoring/datadog.md', '#/properties/config/properties/metrics/items/properties/consumer_identifier')]
enum MetricsConsumerIdentifier: string
{
    case ConsumerId = 'consumer_id';
    case CustomId = 'custom_id';
    case Username = 'username';
}
