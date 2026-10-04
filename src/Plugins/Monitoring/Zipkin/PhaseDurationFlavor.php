<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Zipkin;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.phase_duration_flavor` in the Zipkin Plugin doc.
 *
 * Specify whether to include the duration of each phase as an annotation or a tag.
 */
#[PluginSchema('Monitoring/zipkin.md', '#/properties/config/properties/phase_duration_flavor')]
enum PhaseDurationFlavor: string
{
    case Annotations = 'annotations';
    case Tags = 'tags';
}
