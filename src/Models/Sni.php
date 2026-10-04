<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * An SNI as returned by the Admin API (spec schema `SNI`).
 */
#[Schema('SNI')]
final readonly class Sni implements Model
{
    /**
     * @param ForeignKey        $certificate The id (a UUID) of the certificate with which to associate the SNI hostname.
     * @param string            $name        The SNI name to associate with the given certificate.
     * @param int|null          $createdAt   Unix epoch when the resource was created.
     * @param string|null       $id          A string representing a UUID (universally unique identifier).
     * @param list<string>|null $tags        An optional set of strings associated with the SNIs for grouping and filtering.
     * @param int|null          $updatedAt   Unix epoch when the resource was last updated.
     */
    public function __construct(
        public ForeignKey $certificate,
        public string $name,
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
            certificate: ForeignKey::fromArray(Data::map($data, 'certificate')),
            name: Data::string($data, 'name'),
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
            'certificate' => $this->certificate->toArray(),
            'name' => $this->name,
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'tags' => $this->tags,
            'updated_at' => $this->updatedAt,
        ]);
    }
}
