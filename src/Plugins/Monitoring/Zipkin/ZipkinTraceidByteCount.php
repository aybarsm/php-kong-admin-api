<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Zipkin;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.traceid_byte_count` in the Zipkin Plugin doc.
 *
 * The length in bytes of each request's Trace ID.
 */
#[PluginSchema('Monitoring/zipkin.md', '#/properties/config/properties/traceid_byte_count')]
enum ZipkinTraceidByteCount: int
{
    case Value8 = 8;
    case Value16 = 16;
}
