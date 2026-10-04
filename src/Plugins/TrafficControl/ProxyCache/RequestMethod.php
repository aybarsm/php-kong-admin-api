<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\ProxyCache;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.request_method[]` in the Proxy Cache Plugin doc.
 */
#[PluginSchema('TrafficControl/proxy-cache.md', '#/properties/config/properties/request_method/items')]
enum RequestMethod: string
{
    case Get = 'GET';
    case Head = 'HEAD';
    case Patch = 'PATCH';
    case Post = 'POST';
    case Put = 'PUT';
}
