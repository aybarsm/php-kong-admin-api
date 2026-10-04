<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Enums;

/**
 * Route path handling algorithms.
 *
 * Spec: `components.schemas.RouteJson.path_handling`, `components.schemas.RouteExpression.path_handling`.
 */
enum PathHandling: string
{
    case V0 = 'v0';
    case V1 = 'v1';
}
