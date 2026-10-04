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
     * `errorCode`, `errorName` and `errorFields` come from the error table Kong's core returns for database and
     * validation errors (`{code, name, message, fields}`; Kong `kong/db/errors.lua`, spec-notes Q4). They are
     * read leniently and are null/empty when Kong sends only `{message}` (e.g. 401, 405, 500).
     *
     * @param int|null                $statusCode  HTTP status, or null when no response was received
     * @param string|null             $kongMessage `message` from the error body (spec `BaseError`), when present
     * @param array<string, mixed>    $details     the decoded error body as received
     * @param string|null             $method      HTTP method of the failed request
     * @param string|null             $path        request path (without query) of the failed request
     * @param int|null                $errorCode   Kong error code (e.g. 2 schema violation, 5 unique constraint violation)
     * @param string|null             $errorName   Kong error name (e.g. `schema violation`)
     * @param array<array-key, mixed> $errorFields per-field messages (`fields`, or `options` for invalid options), possibly nested
     */
    public function __construct(
        string $message,
        public readonly ?int $statusCode = null,
        public readonly ?string $kongMessage = null,
        public readonly array $details = [],
        public readonly ?string $method = null,
        public readonly ?string $path = null,
        ?Throwable $previous = null,
        public readonly ?int $errorCode = null,
        public readonly ?string $errorName = null,
        public readonly array $errorFields = [],
    ) {
        parent::__construct($message, $statusCode ?? 0, $previous);
    }
}
