<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Zipkin;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.propagation.default_format` in the Zipkin Plugin doc.
 *
 * The default header format to use when extractors did not match any format in the incoming headers and `inject…
 */
#[PluginSchema('Monitoring/zipkin.md', '#/properties/config/properties/propagation/properties/default_format')]
enum ZipkinPropagationDefaultFormat: string
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
