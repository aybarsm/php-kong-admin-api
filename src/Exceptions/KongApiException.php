<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Exceptions;

use RuntimeException;
use Throwable;

/**
 * Base class for every Admin API or transport failure.
 *
 * Thrown as-is for HTTP error statuses without a dedicated subclass (e.g. 403, 405).
 */
class KongApiException extends RuntimeException implements KongExceptionInterface
{
    /**
     * @param int|null             $statusCode  HTTP status, or null when no response was received
     * @param string|null          $kongMessage `message` from the spec's error body (`BaseError`), when present
     * @param array<string, mixed> $details     the decoded error body as received; only `message` and `status` are spec-defined
     * @param string|null          $method      HTTP method of the failed request
     * @param string|null          $path        request path (without query) of the failed request
     */
    public function __construct(
        string $message,
        public readonly ?int $statusCode = null,
        public readonly ?string $kongMessage = null,
        public readonly array $details = [],
        public readonly ?string $method = null,
        public readonly ?string $path = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode ?? 0, $previous);
    }
}
