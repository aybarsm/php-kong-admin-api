<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\ProxyCache;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.strategy` in the Proxy Cache Plugin doc.
 *
 * The backing data store in which to hold cache entities.
 */
#[PluginSchema('TrafficControl/proxy-cache.md', '#/properties/config/properties/strategy')]
enum Strategy: string
{
    case Memory = 'memory';
}
