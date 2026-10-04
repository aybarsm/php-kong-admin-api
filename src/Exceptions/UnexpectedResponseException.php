<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Exceptions;

/**
 * A response that cannot be decoded or does not match the spec schema.
 */
final class UnexpectedResponseException extends KongApiException
{
}
