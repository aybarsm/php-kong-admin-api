<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * An ACL (Consumer group membership for the ACL plugin) as returned by the Admin API (spec schema `ACL`).
 */
#[Schema('ACL')]
final readonly class Acl implements Model
{
    /**
     * @param string            $group
     * @param ForeignKey|null   $consumer
     * @param int|null          $createdAt Unix epoch when the resource was created.
     * @param string|null       $id        A string representing a UUID (universally unique identifier).
     * @param list<string>|null $tags      A set of strings representing tags.
     */
    public function __construct(
        public string $group,
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
            group: Data::string($data, 'group'),
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
            'group' => $this->group,
            'consumer' => $this->consumer?->toArray(),
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'tags' => $this->tags,
        ]);
    }
}
