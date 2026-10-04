<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * A Basic-auth credential as returned by the Admin API (spec schema `BasicAuth`).
 */
#[Schema('BasicAuth')]
final readonly class BasicAuth implements Model
{
    /** Properties the spec marks `x-encrypted`; redacted in __debugInfo(). */
    private const array ENCRYPTED = ['password'];

    /**
     * @param string            $password
     * @param string            $username
     * @param ForeignKey|null   $consumer
     * @param int|null          $createdAt Unix epoch when the resource was created.
     * @param string|null       $id        A string representing a UUID (universally unique identifier).
     * @param list<string>|null $tags      A set of strings representing tags.
     */
    public function __construct(
        public string $password,
        public string $username,
        public ?ForeignKey $consumer = null,
        public ?int $createdAt = null,
        public ?string $id = null,
        public ?array $tags = null,
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
            password: Data::string($data, 'password'),
            username: Data::string($data, 'username'),
            consumer: $consumer === null ? null : ForeignKey::fromArray($consumer),
            createdAt: Data::intOrNull($data, 'created_at'),
            id: Data::stringOrNull($data, 'id'),
            tags: Data::stringListOrNull($data, 'tags'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'password' => $this->password,
            'username' => $this->username,
            'consumer' => $this->consumer?->toArray(),
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'tags' => $this->tags,
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
