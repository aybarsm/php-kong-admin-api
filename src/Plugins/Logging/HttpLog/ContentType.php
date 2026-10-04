<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Logging\HttpLog;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.content_type` in the HTTP Log Plugin doc.
 *
 * Indicates the type of data sent.
 */
#[PluginSchema('Logging/http-log.md', '#/properties/config/properties/content_type')]
enum ContentType: string
{
    case ApplicationJson = 'application/json';
    case ApplicationJsonCharsetUtf8 = 'application/json; charset=utf-8';
}
