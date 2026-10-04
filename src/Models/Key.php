<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * A Key (JWK or PEM) as returned by the Admin API (spec schema `Key`).
 */
#[Schema('Key')]
final readonly class Key implements Model
{
    /** Properties the spec marks `x-encrypted`; redacted in __debugInfo(). */
    private const array ENCRYPTED = ['jwk'];

    /**
     * @param string            $kid       A unique identifier for a key.
     * @param int|null          $createdAt Unix epoch when the resource was created.
     * @param string|null       $id        A string representing a UUID (universally unique identifier).
     * @param string|null       $jwk       A JSON Web Key represented as a string.
     * @param string|null       $name      The name to associate with the given keys.
     * @param KeyPem|null       $pem       A keypair in PEM format.
     * @param ForeignKey|null   $set       The id (an UUID) of the key-set with which to associate the key.
     * @param list<string>|null $tags      An optional set of strings associated with the Key for grouping and filtering.
     * @param int|null          $updatedAt Unix epoch when the resource was last updated.
     * @param string|null       $x5t       X.509 certificate SHA-1 thumbprint.
     */
    public function __construct(
        public string $kid,
        public ?int $createdAt = null,
        public ?string $id = null,
        public ?string $jwk = null,
        public ?string $name = null,
        public ?KeyPem $pem = null,
        public ?ForeignKey $set = null,
        public ?array $tags = null,
        public ?int $updatedAt = null,
        public ?string $x5t = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $pem = Data::mapOrNull($data, 'pem');
        $set = Data::mapOrNull($data, 'set');

        return new self(
            kid: Data::string($data, 'kid'),
            createdAt: Data::intOrNull($data, 'created_at'),
            id: Data::stringOrNull($data, 'id'),
            jwk: Data::stringOrNull($data, 'jwk'),
            name: Data::stringOrNull($data, 'name'),
            pem: $pem === null ? null : KeyPem::fromArray($pem),
            set: $set === null ? null : ForeignKey::fromArray($set),
            tags: Data::stringListOrNull($data, 'tags'),
            updatedAt: Data::intOrNull($data, 'updated_at'),
            x5t: Data::stringOrNull($data, 'x5t'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'kid' => $this->kid,
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'jwk' => $this->jwk,
            'name' => $this->name,
            'pem' => $this->pem?->toArray(),
            'set' => $this->set?->toArray(),
            'tags' => $this->tags,
            'updated_at' => $this->updatedAt,
            'x5t' => $this->x5t,
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
