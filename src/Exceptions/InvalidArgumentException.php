<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Exceptions;

use InvalidArgumentException as BaseInvalidArgumentException;

/**
 * A caller error detected before any request is sent (e.g. page size out of the spec's bounds).
 */
final class InvalidArgumentException extends BaseInvalidArgumentException implements KongExceptionInterface
{
}
