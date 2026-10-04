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
 * Request body for creating, updating or upserting a Key (JWK or PEM)
 * (spec schema `Key`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('Key')]
final readonly class KeyInput implements Input
{
    /** Properties the spec marks `x-encrypted` (plus reviewed secrets, spec-notes Q18); redacted in __debugInfo(). */
    private const array ENCRYPTED = ['jwk'];

    /**
     * @param string|null            $kid       A unique identifier for a key. Required by the spec on create.
     * @param int|null               $createdAt Unix epoch when the resource was created.
     * @param string|null            $id        A string representing a UUID (universally unique identifier).
     * @param string|null            $jwk       A JSON Web Key represented as a string.
     * @param string|null            $name      The name to associate with the given keys.
     * @param KeyPem|null            $pem       A keypair in PEM format.
     * @param ForeignKey|string|null $set       The id (an UUID) of the key-set with which to associate the key.
     * @param list<string>|null      $tags      An optional set of strings associated with the Key for grouping and filtering.
     * @param int|null               $updatedAt Unix epoch when the resource was last updated.
     * @param string|null            $x5t       X.509 certificate SHA-1 thumbprint.
     */
    public function __construct(
        public ?string $kid = null,
        public ?int $createdAt = null,
        public ?string $id = null,
        #[SensitiveParameter]
        public ?string $jwk = null,
        public ?string $name = null,
        public ?KeyPem $pem = null,
        public ForeignKey|string|null $set = null,
        public ?array $tags = null,
        public ?int $updatedAt = null,
        public ?string $x5t = null,
    ) {
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
            'set' => $this->set === null ? null : ForeignKey::of($this->set)->toArray(),
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
