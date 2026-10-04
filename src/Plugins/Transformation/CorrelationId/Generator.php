<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Transformation\CorrelationId;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.generator` in the Correlation ID Plugin doc.
 *
 * The generator to use for the correlation ID.
 */
#[PluginSchema('Transformation/correlation-id.md', '#/properties/config/properties/generator')]
enum Generator: string
{
    case Tracker = 'tracker';
    case Uuid = 'uuid';
    case UuidCounter = 'uuid#counter';
}
