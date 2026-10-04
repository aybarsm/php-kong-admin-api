<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Request body for creating, updating or upserting a CA Certificate
 * (spec schema `CACertificate`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('CACertificate')]
final readonly class CaCertificateInput implements Input
{
    /**
     * @param string|null       $cert       PEM-encoded public certificate of the CA. Required by the spec on create.
     * @param string|null       $certDigest SHA256 hex digest of the public certificate.
     * @param int|null          $createdAt  Unix epoch when the resource was created.
     * @param string|null       $id         A string representing a UUID (universally unique identifier).
     * @param list<string>|null $tags       An optional set of strings associated with the Certificate for grouping and filtering.
     * @param int|null          $updatedAt  Unix epoch when the resource was last updated.
     */
    public function __construct(
        public ?string $cert = null,
        public ?string $certDigest = null,
        public ?int $createdAt = null,
        public ?string $id = null,
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
            'cert_digest' => $this->certDigest,
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'tags' => $this->tags,
            'updated_at' => $this->updatedAt,
        ]);
    }
}
