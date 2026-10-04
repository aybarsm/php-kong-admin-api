<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * An API key (key-auth credential) as returned by the Admin API (spec schema `KeyAuth`).
 */
#[Schema('KeyAuth')]
final readonly class KeyAuth implements Model
{
    /**
     * @param ForeignKey|null   $consumer
     * @param int|null          $createdAt Unix epoch when the resource was created.
     * @param string|null       $id        A string representing a UUID (universally unique identifier).
     * @param string|null       $key
     * @param list<string>|null $tags      A set of strings representing tags.
     * @param int|null          $ttl       key-auth ttl in seconds
     */
    public function __construct(
        public ?ForeignKey $consumer = null,
        public ?int $createdAt = null,
        public ?string $id = null,
        public ?string $key = null,
        public ?array $tags = null,
        public ?int $ttl = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $consumer = Data::mapOrNull($data, 'consumer');

        return new self(
            consumer: $consumer === null ? null : ForeignKey::fromArray($consumer),
            createdAt: Data::intOrNull($data, 'created_at'),
            id: Data::stringOrNull($data, 'id'),
            key: Data::stringOrNull($data, 'key'),
            tags: Data::stringListOrNull($data, 'tags'),
            ttl: Data::intOrNull($data, 'ttl'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'consumer' => $this->consumer?->toArray(),
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'key' => $this->key,
            'tags' => $this->tags,
            'ttl' => $this->ttl,
        ]);
    }
}
