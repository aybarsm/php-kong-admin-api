<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Security\Cors;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.methods[]` in the CORS Plugin doc.
 */
#[PluginSchema('Security/cors.md', '#/properties/config/properties/methods/items')]
enum Methods: string
{
    case Connect = 'CONNECT';
    case Delete = 'DELETE';
    case Get = 'GET';
    case Head = 'HEAD';
    case Options = 'OPTIONS';
    case Patch = 'PATCH';
    case Post = 'POST';
    case Put = 'PUT';
    case Trace = 'TRACE';
}
