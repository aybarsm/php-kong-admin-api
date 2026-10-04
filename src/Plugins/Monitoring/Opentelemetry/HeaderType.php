<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Opentelemetry;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.header_type` in the OpenTelemetry Plugin doc.
 */
#[PluginSchema('Monitoring/opentelemetry.md', '#/properties/config/properties/header_type')]
enum HeaderType: string
{
    case Aws = 'aws';
    case B3 = 'b3';
    case B3Single = 'b3-single';
    case Datadog = 'datadog';
    case Gcp = 'gcp';
    case Ignore = 'ignore';
    case Instana = 'instana';
    case Jaeger = 'jaeger';
    case Ot = 'ot';
    case Preserve = 'preserve';
    case W3c = 'w3c';
}
