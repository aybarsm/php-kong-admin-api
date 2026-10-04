<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Exceptions;

/**
 * The request failed before a response was received (network, DNS, TLS or client error).
 */
final class TransportException extends KongApiException
{
}
