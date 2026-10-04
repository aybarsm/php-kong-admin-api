<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Exceptions;

/**
 * HTTP 401 Unauthorized returned by the Admin API (spec: `HTTP401Error`).
 */
final class UnauthorizedException extends KongApiException
{
}
