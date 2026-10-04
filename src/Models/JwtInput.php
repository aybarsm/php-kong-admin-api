<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Enums\JwtAlgorithm;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * Request body for creating, updating or upserting a JWT credential
 * (spec schema `JWT`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('JWT')]
final readonly class JwtInput implements Input
{
    /**
     * @param JwtAlgorithm|null      $algorithm
     * @param ForeignKey|string|null $consumer
     * @param int|null               $createdAt    Unix epoch when the resource was created.
     * @param string|null            $id           A string representing a UUID (universally unique identifier).
     * @param string|null            $key
     * @param string|null            $rsaPublicKey
     * @param string|null            $secret
     * @param list<string>|null      $tags         A set of strings representing tags.
     */
    public function __construct(
        public ?JwtAlgorithm $algorithm = null,
        public ForeignKey|string|null $consumer = null,
        public ?int $createdAt = null,
        public ?string $id = null,
        public ?string $key = null,
        public ?string $rsaPublicKey = null,
        public ?string $secret = null,
        public ?array $tags = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'algorithm' => $this->algorithm?->value,
            'consumer' => $this->consumer === null ? null : ForeignKey::of($this->consumer)->toArray(),
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'key' => $this->key,
            'rsa_public_key' => $this->rsaPublicKey,
            'secret' => $this->secret,
            'tags' => $this->tags,
        ]);
    }
}
