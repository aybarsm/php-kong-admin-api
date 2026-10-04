<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Attributes;

use Attribute;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;

/**
 * Links a public resource method to the spec operation it implements.
 *
 * `path` is the spec path template verbatim. For `OperationScope::Both` the `/{workspace}`
 * twin must also exist in the spec; for `OperationScope::WorkspaceOnly` the path itself
 * starts with `/{workspace}`.
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
final readonly class Operation
{
    public function __construct(
        public string $method,
        public string $path,
        public string $operationId,
        public OperationScope $scope,
    ) {
    }
}
