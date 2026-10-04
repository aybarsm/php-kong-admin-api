<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Enums;

/**
 * Rate-limiting window types for a Consumer Group's rate-limiting-advanced override.
 *
 * Spec: `components.requestBodies.consumerGroupsConfigResponse` property `config.window_type`.
 */
enum RateLimitWindowType: string
{
    case Sliding = 'sliding';
    case Fixed = 'fixed';
}
