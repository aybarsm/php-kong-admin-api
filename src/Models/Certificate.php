<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * A Certificate as returned by the Admin API (spec schema `Certificate`).
 */
#[Schema('Certificate')]
final readonly class Certificate implements Model
{
    /** Properties the spec marks `x-encrypted` (plus reviewed secrets, spec-notes Q18); redacted in __debugInfo(). */
    private const array ENCRYPTED = ['key', 'keyAlt'];

    /**
     * @param string            $cert      PEM-encoded public certificate chain of the SSL key pair.
     * @param string            $key       PEM-encoded private key of the SSL key pair.
     * @param string|null       $certAlt   PEM-encoded public certificate chain of the alternate SSL key pair.
     * @param int|null          $createdAt Unix epoch when the resource was created.
     * @param string|null       $id        A string representing a UUID (universally unique identifier).
     * @param string|null       $keyAlt    PEM-encoded private key of the alternate SSL key pair.
     * @param list<string>|null $snis
     * @param list<string>|null $tags      An optional set of strings associated with the Certificate for grouping and filtering.
     * @param int|null          $updatedAt Unix epoch when the resource was last updated.
     */
    public function __construct(
        public string $cert,
        public string $key,
        public ?string $certAlt = null,
        public ?int $createdAt = null,
        public ?string $id = null,
        public ?string $keyAlt = null,
        public ?array $snis = null,
        public ?array $tags = null,
        public ?int $updatedAt = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            cert: Data::string($data, 'cert'),
            key: Data::string($data, 'key'),
            certAlt: Data::stringOrNull($data, 'cert_alt'),
            createdAt: Data::intOrNull($data, 'created_at'),
            id: Data::stringOrNull($data, 'id'),
            keyAlt: Data::stringOrNull($data, 'key_alt'),
            snis: Data::stringListOrNull($data, 'snis'),
            tags: Data::stringListOrNull($data, 'tags'),
            updatedAt: Data::intOrNull($data, 'updated_at'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'cert' => $this->cert,
            'key' => $this->key,
            'cert_alt' => $this->certAlt,
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'key_alt' => $this->keyAlt,
            'snis' => $this->snis,
            'tags' => $this->tags,
            'updated_at' => $this->updatedAt,
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
