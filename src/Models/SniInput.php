<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * Request body for creating, updating or upserting an SNI
 * (spec schema `SNI`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('SNI')]
final readonly class SniInput implements Input
{
    /**
     * @param ForeignKey|string|null $certificate The id (a UUID) of the certificate with which to associate the SNI hostname. Required by the spec on create.
     * @param string|null            $name        The SNI name to associate with the given certificate. Required by the spec on create.
     * @param int|null               $createdAt   Unix epoch when the resource was created.
     * @param string|null            $id          A string representing a UUID (universally unique identifier).
     * @param list<string>|null      $tags        An optional set of strings associated with the SNIs for grouping and filtering.
     * @param int|null               $updatedAt   Unix epoch when the resource was last updated.
     */
    public function __construct(
        public ForeignKey|string|null $certificate = null,
        public ?string $name = null,
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
            'certificate' => $this->certificate === null ? null : ForeignKey::of($this->certificate)->toArray(),
            'name' => $this->name,
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'tags' => $this->tags,
            'updated_at' => $this->updatedAt,
        ]);
    }
}
