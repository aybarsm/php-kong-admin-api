<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Statsd;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.service_identifier_default` in the StatsD Plugin doc.
 */
#[PluginSchema('Monitoring/statsd.md', '#/properties/config/properties/service_identifier_default')]
enum StatsdServiceIdentifierDefault: string
{
    case ServiceHost = 'service_host';
    case ServiceId = 'service_id';
    case ServiceName = 'service_name';
    case ServiceNameOrHost = 'service_name_or_host';
}
