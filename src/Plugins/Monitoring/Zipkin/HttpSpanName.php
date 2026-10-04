<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Zipkin;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.http_span_name` in the Zipkin Plugin doc.
 *
 * Specify whether to include the HTTP path in the span name.
 */
#[PluginSchema('Monitoring/zipkin.md', '#/properties/config/properties/http_span_name')]
enum HttpSpanName: string
{
    case Method = 'method';
    case MethodPath = 'method_path';
}
