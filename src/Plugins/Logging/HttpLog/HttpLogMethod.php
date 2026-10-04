<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Logging\HttpLog;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.method` in the HTTP Log Plugin doc.
 *
 * An optional method used to send data to the HTTP server.
 */
#[PluginSchema('Logging/http-log.md', '#/properties/config/properties/method')]
enum HttpLogMethod: string
{
    case Patch = 'PATCH';
    case Post = 'POST';
    case Put = 'PUT';
}
