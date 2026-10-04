<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Enums;

/**
 * Partial variants: the `type` discriminator of the spec's `Partial` oneOf.
 *
 * Spec: `components.schemas.Partial.discriminator.mapping`.
 */
enum PartialType: string
{
    case Embeddings = 'embeddings';
    case Model = 'model';
    case RedisCe = 'redis-ce';
    case RedisEe = 'redis-ee';
    case Vectordb = 'vectordb';
}
