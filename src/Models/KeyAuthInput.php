<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;
use SensitiveParameter;

/**
 * Request body for creating, updating or upserting an API key (key-auth credential)
 * (spec schema `KeyAuth`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('KeyAuth')]
final readonly class KeyAuthInput implements Input
{
    /** Properties the spec marks `x-encrypted` (plus reviewed secrets, spec-notes Q18); redacted in __debugInfo(). */
    private const array ENCRYPTED = ['key'];

    /**
     * @param ForeignKey|string|null $consumer
     * @param int|null               $createdAt Unix epoch when the resource was created.
     * @param string|null            $id        A string representing a UUID (universally unique identifier).
     * @param string|null            $key
     * @param list<string>|null      $tags      A set of strings representing tags.
     * @param int|null               $ttl       key-auth ttl in seconds
     */
    public function __construct(
        public ForeignKey|string|null $consumer = null,
        public ?int $createdAt = null,
        public ?string $id = null,
        #[SensitiveParameter]
        public ?string $key = null,
        public ?array $tags = null,
        public ?int $ttl = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'consumer' => $this->consumer === null ? null : ForeignKey::of($this->consumer)->toArray(),
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'key' => $this->key,
            'tags' => $this->tags,
            'ttl' => $this->ttl,
        ]);
    }

    /**
     * Redacts `x-encrypted` values.
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        $values = get_object_vars($this);
        foreach (self::ENCRYPTED as $property) {
            if ($values[$property] !== null) {
                $values[$property] = '***';
            }
        }

        return $values;
    }
}
