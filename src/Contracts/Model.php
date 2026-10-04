<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Contracts;

use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;

/**
 * A typed DTO mapped from an Admin API response payload.
 */
interface Model
{
    /**
     * Build the DTO from a decoded JSON object.
     *
     * @param array<string, mixed> $data
     *
     * @throws UnexpectedResponseException when the payload does not match the spec schema
     */
    public static function fromArray(array $data): static;

    /**
     * The DTO as a JSON-ready array using the spec's (snake_case) property names; null values are omitted.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
