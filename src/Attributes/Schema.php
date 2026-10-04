<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Attributes;

use Attribute;

/**
 * Names the spec component schema (`components.schemas.<name>`) a DTO is derived from.
 */
#[Attribute(Attribute::TARGET_CLASS)]
final readonly class Schema
{
    public function __construct(
        public string $name,
    ) {
    }
}
