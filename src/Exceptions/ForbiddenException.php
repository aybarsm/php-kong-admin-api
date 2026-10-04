<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Exceptions;

/**
 * HTTP 403 Forbidden returned by the Admin API (e.g. an RBAC permission failure).
 *
 * The spec defines no 403 response; this follows standard HTTP semantics (spec-notes Q12).
 */
final class ForbiddenException extends KongApiException
{
}
