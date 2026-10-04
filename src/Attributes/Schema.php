<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Attributes;

use Attribute;

/**
 * Names the spec schema a DTO is derived from: either a component schema name
 * (`components.schemas.<name>`) or a JSON pointer into the spec for inline objects,
 * e.g. `#/components/schemas/Upstream/properties/healthchecks`.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final readonly class Schema
{
    /**
     * @param string $name component schema name, or a JSON pointer starting with `#/`
     */
    public function __construct(
        public string $name,
    ) {
    }
}
