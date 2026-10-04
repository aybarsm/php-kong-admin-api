<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Zipkin;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.propagation.extract[]` in the Zipkin Plugin doc.
 */
#[PluginSchema('Monitoring/zipkin.md', '#/properties/config/properties/propagation/properties/extract/items')]
enum PropagationExtract: string
{
    case Aws = 'aws';
    case B3 = 'b3';
    case Datadog = 'datadog';
    case Gcp = 'gcp';
    case Instana = 'instana';
    case Jaeger = 'jaeger';
    case Ot = 'ot';
    case W3c = 'w3c';
}
