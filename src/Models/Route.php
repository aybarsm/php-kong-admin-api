<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Contracts\Model;

/**
 * A Route: the spec's `Route` schema is `oneOf [RouteJson, RouteExpression]` without a discriminator.
 *
 * Responses are mapped by RouteFactory: a payload with a non-null `expression` is a RouteExpression,
 * any other payload is a RouteJson (spec-notes Q1, decided 2026-10-04). Use `instanceof` to branch.
 */
interface Route extends Model
{
}
