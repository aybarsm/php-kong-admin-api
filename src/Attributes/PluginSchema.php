<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Attributes;

use Attribute;

/**
 * Names the plugin doc a plugin class or enum is derived from: the doc file relative to
 * `resources/kong-admin-api/plugins/` (e.g. `TrafficControl/rate-limiting.md`) and a JSON pointer into the
 * doc's JSON Schema block (`#` for the root, `#/properties/config`, `#/properties/config/properties/redis`).
 */
#[Attribute(Attribute::TARGET_CLASS)]
final readonly class PluginSchema
{
    /**
     * @param string $doc     doc file relative to `resources/kong-admin-api/plugins/`
     * @param string $pointer JSON pointer into the doc schema, starting with `#`
     */
    public function __construct(
        public string $doc,
        public string $pointer,
    ) {
    }
}
