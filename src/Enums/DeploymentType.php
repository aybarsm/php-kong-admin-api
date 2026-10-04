<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Enums;

/**
 * Kong deployment modes reported by the license report.
 *
 * Spec: `components.responses.ReportResponse.content.application/json.schema.deployment_info.type`.
 */
enum DeploymentType: string
{
    case Traditional = 'traditional';
    case Hybrid = 'hybrid';
    case Dbless = 'dbless';
}
