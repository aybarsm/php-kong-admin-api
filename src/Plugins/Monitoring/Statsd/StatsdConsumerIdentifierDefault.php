<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Statsd;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.consumer_identifier_default` in the StatsD Plugin doc.
 */
#[PluginSchema('Monitoring/statsd.md', '#/properties/config/properties/consumer_identifier_default')]
enum StatsdConsumerIdentifierDefault: string
{
    case ConsumerId = 'consumer_id';
    case CustomId = 'custom_id';
    case Username = 'username';
}
