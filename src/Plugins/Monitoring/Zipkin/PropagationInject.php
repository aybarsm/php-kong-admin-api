<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Zipkin;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.propagation.inject[]` in the Zipkin Plugin doc.
 */
#[PluginSchema('Monitoring/zipkin.md', '#/properties/config/properties/propagation/properties/inject/items')]
enum PropagationInject: string
{
    case Aws = 'aws';
    case B3 = 'b3';
    case B3Single = 'b3-single';
    case Datadog = 'datadog';
    case Gcp = 'gcp';
    case Instana = 'instana';
    case Jaeger = 'jaeger';
    case Ot = 'ot';
    case Preserve = 'preserve';
    case W3c = 'w3c';
}
