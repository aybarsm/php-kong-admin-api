<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * A CA Certificate as returned by the Admin API (spec schema `CACertificate`).
 */
#[Schema('CACertificate')]
final readonly class CaCertificate implements Model
{
    /**
     * @param string            $cert       PEM-encoded public certificate of the CA.
     * @param string|null       $certDigest SHA256 hex digest of the public certificate.
     * @param int|null          $createdAt  Unix epoch when the resource was created.
     * @param string|null       $id         A string representing a UUID (universally unique identifier).
     * @param list<string>|null $tags       An optional set of strings associated with the Certificate for grouping and filtering.
     * @param int|null          $updatedAt  Unix epoch when the resource was last updated.
     */
    public function __construct(
        public string $cert,
        public ?string $certDigest = null,
        public ?int $createdAt = null,
        public ?string $id = null,
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
            certDigest: Data::stringOrNull($data, 'cert_digest'),
            createdAt: Data::intOrNull($data, 'created_at'),
            id: Data::stringOrNull($data, 'id'),
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
            'cert_digest' => $this->certDigest,
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'tags' => $this->tags,
            'updated_at' => $this->updatedAt,
        ]);
    }
}
