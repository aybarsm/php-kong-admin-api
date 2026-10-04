<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;
use SensitiveParameter;

/**
 * Request body for creating, updating or upserting a Certificate
 * (spec schema `Certificate`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('Certificate')]
final readonly class CertificateInput implements Input
{
    /** Properties the spec marks `x-encrypted`; redacted in __debugInfo(). */
    private const array ENCRYPTED = ['key', 'keyAlt'];

    /**
     * @param string|null       $cert      PEM-encoded public certificate chain of the SSL key pair. Required by the spec on create.
     * @param string|null       $key       PEM-encoded private key of the SSL key pair. Required by the spec on create.
     * @param string|null       $certAlt   PEM-encoded public certificate chain of the alternate SSL key pair.
     * @param int|null          $createdAt Unix epoch when the resource was created.
     * @param string|null       $id        A string representing a UUID (universally unique identifier).
     * @param string|null       $keyAlt    PEM-encoded private key of the alternate SSL key pair.
     * @param list<string>|null $snis
     * @param list<string>|null $tags      An optional set of strings associated with the Certificate for grouping and filtering.
     * @param int|null          $updatedAt Unix epoch when the resource was last updated.
     */
    public function __construct(
        public ?string $cert = null,
        #[SensitiveParameter]
        public ?string $key = null,
        public ?string $certAlt = null,
        public ?int $createdAt = null,
        public ?string $id = null,
        #[SensitiveParameter]
        public ?string $keyAlt = null,
        public ?array $snis = null,
        public ?array $tags = null,
        public ?int $updatedAt = null,
    ) {
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
