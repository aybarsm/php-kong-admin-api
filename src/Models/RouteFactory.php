<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;

/**
 * Maps a Route payload to the matching `oneOf` variant of the spec's `Route` schema.
 *
 * Only `RouteExpression` defines `expression`, and `RouteJson` forbids additional properties, so a
 * non-null `expression` identifies a RouteExpression. Everything else is a RouteJson (spec-notes Q1).
 */
final readonly class RouteFactory
{
    /** @codeCoverageIgnore Static factory; never instantiated. */
    private function __construct()
    {
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws UnexpectedResponseException
     */
    public static function fromArray(array $data): Route
    {
        return Data::stringOrNull($data, 'expression') !== null
            ? RouteExpression::fromArray($data)
            : RouteJson::fromArray($data);
    }
}
