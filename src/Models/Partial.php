<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Contracts\Model;

/**
 * A Partial: the spec's `Partial` schema is a `oneOf` of five variants with the discriminator `type`.
 *
 * Responses are mapped by PartialFactory (PartialRedisCe, PartialRedisEe, PartialVectordb,
 * PartialEmbeddings, PartialModel). Use `instanceof` to branch.
 */
interface Partial extends Model
{
}
