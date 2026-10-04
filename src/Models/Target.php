<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * A Target of an Upstream as returned by the Admin API (spec schema `Target`).
 *
 * The spec types `created_at`/`updated_at` as `number` here, unlike other entities (spec-notes Q14).
 */
#[Schema('Target')]
final readonly class Target implements Model
{
    /**
     * @param string            $target    The target address (ip or hostname) and port.
     * @param float|null        $createdAt Unix epoch when the resource was created.
     * @param bool|null         $failover  Whether to use this target only as backup or not.
     * @param string|null       $id        A string representing a UUID (universally unique identifier).
     * @param list<string>|null $tags      An optional set of strings associated with the Target for grouping and filtering.
     * @param float|null        $updatedAt Unix epoch when the resource was last updated.
     * @param ForeignKey|null   $upstream  The unique identifier or the name of the upstream for which to update the target.
     * @param int|null          $weight    The weight this target gets within the upstream loadbalancer (`0`-`65535`).
     */
    public function __construct(
        public string $target,
        public ?float $createdAt = null,
        public ?bool $failover = null,
        public ?string $id = null,
        public ?array $tags = null,
        public ?float $updatedAt = null,
        public ?ForeignKey $upstream = null,
        public ?int $weight = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $upstream = Data::mapOrNull($data, 'upstream');

        return new self(
            target: Data::string($data, 'target'),
            createdAt: Data::floatOrNull($data, 'created_at'),
            failover: Data::boolOrNull($data, 'failover'),
            id: Data::stringOrNull($data, 'id'),
            tags: Data::stringListOrNull($data, 'tags'),
            updatedAt: Data::floatOrNull($data, 'updated_at'),
            upstream: $upstream === null ? null : ForeignKey::fromArray($upstream),
            weight: Data::intOrNull($data, 'weight'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'target' => $this->target,
            'created_at' => $this->createdAt,
            'failover' => $this->failover,
            'id' => $this->id,
            'tags' => $this->tags,
            'updated_at' => $this->updatedAt,
            'upstream' => $this->upstream?->toArray(),
            'weight' => $this->weight,
        ]);
    }
}
