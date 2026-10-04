<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\RequestSizeLimiting;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.size_unit` in the Request Size Limiting Plugin doc.
 *
 * Size unit can be set either in `bytes`, `kilobytes`, or `megabytes` (default).
 */
#[PluginSchema('TrafficControl/request-size-limiting.md', '#/properties/config/properties/size_unit')]
enum SizeUnit: string
{
    case Bytes = 'bytes';
    case Kilobytes = 'kilobytes';
    case Megabytes = 'megabytes';
}
