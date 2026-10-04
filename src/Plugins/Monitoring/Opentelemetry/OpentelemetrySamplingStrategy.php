<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Opentelemetry;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.sampling_strategy` in the OpenTelemetry Plugin doc.
 *
 * The sampling strategy to use for OTLP `traces`.
 */
#[PluginSchema('Monitoring/opentelemetry.md', '#/properties/config/properties/sampling_strategy')]
enum OpentelemetrySamplingStrategy: string
{
    case ParentDropProbabilityFallback = 'parent_drop_probability_fallback';
    case ParentProbabilityFallback = 'parent_probability_fallback';
}
