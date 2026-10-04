<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * Request body for creating, updating or upserting an MTLS-auth credential
 * (spec schema `MTLSAuth`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('MTLSAuth')]
final readonly class MtlsAuthInput implements Input
{
    /**
     * @param string|null            $subjectName   Required by the spec on create.
     * @param ForeignKey|string|null $caCertificate
     * @param ForeignKey|string|null $consumer
     * @param int|null               $createdAt     Unix epoch when the resource was created.
     * @param string|null            $id            A string representing a UUID (universally unique identifier).
     * @param list<string>|null      $tags          A set of strings representing tags.
     */
    public function __construct(
        public ?string $subjectName = null,
        public ForeignKey|string|null $caCertificate = null,
        public ForeignKey|string|null $consumer = null,
        public ?int $createdAt = null,
        public ?string $id = null,
        public ?array $tags = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'subject_name' => $this->subjectName,
            'ca_certificate' => $this->caCertificate === null ? null : ForeignKey::of($this->caCertificate)->toArray(),
            'consumer' => $this->consumer === null ? null : ForeignKey::of($this->consumer)->toArray(),
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'tags' => $this->tags,
        ]);
    }
}
