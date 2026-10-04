<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Zipkin;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.default_header_type` in the Zipkin Plugin doc.
 *
 * Allows specifying the type of header to be added to requests with no pre-existing tracing headers and when `c…
 */
#[PluginSchema('Monitoring/zipkin.md', '#/properties/config/properties/default_header_type')]
enum DefaultHeaderType: string
{
    case Aws = 'aws';
    case B3 = 'b3';
    case B3Single = 'b3-single';
    case Datadog = 'datadog';
    case Gcp = 'gcp';
    case Instana = 'instana';
    case Jaeger = 'jaeger';
    case Ot = 'ot';
    case W3c = 'w3c';
}
