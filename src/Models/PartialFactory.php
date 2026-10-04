<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Enums\PartialType;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;

/**
 * Maps a Partial payload to its variant using the spec discriminator `type`
 * (`components.schemas.Partial.discriminator.mapping`).
 */
final readonly class PartialFactory
{
    /** @codeCoverageIgnore Static factory; never instantiated. */
    private function __construct()
    {
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws UnexpectedResponseException when `type` is missing or not a spec Partial type
     */
    public static function fromArray(array $data): Partial
    {
        $type = Data::string($data, 'type');

        return match (PartialType::tryFrom($type)) {
            PartialType::RedisCe => PartialRedisCe::fromArray($data),
            PartialType::RedisEe => PartialRedisEe::fromArray($data),
            PartialType::Vectordb => PartialVectordb::fromArray($data),
            PartialType::Embeddings => PartialEmbeddings::fromArray($data),
            PartialType::Model => PartialModel::fromArray($data),
            null => throw new UnexpectedResponseException(sprintf('Unknown Partial type "%s".', $type)),
        };
    }
}
