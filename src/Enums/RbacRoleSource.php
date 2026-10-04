<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Enums;

/**
 * Origin of an RBAC user role assignment.
 *
 * Spec: `components.schemas.RBACUserRole.role_source`.
 */
enum RbacRoleSource: string
{
    case Idp = 'idp';
    case Local = 'local';
}
