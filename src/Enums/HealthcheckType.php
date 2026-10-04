<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Enums;

/**
 * Upstream health check types.
 *
 * Spec: `components.schemas.Upstream.healthchecks.active.type`, `components.schemas.Upstream.healthchecks.passive.type`.
 */
enum HealthcheckType: string
{
    case Grpc = 'grpc';
    case Grpcs = 'grpcs';
    case Http = 'http';
    case Https = 'https';
    case Tcp = 'tcp';
}
