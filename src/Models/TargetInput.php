<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * Request body for creating, updating or upserting a Target of an Upstream
 * (spec schema `Target`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('Target')]
final readonly class TargetInput implements Input
{
    /**
     * @param string|null            $target    The target address (ip or hostname) and port. Required by the spec on create.
     * @param float|null             $createdAt Unix epoch when the resource was created.
     * @param bool|null              $failover  Whether to use this target only as backup or not.
     * @param string|null            $id        A string representing a UUID (universally unique identifier).
     * @param list<string>|null      $tags      An optional set of strings associated with the Target for grouping and filtering.
     * @param float|null             $updatedAt Unix epoch when the resource was last updated.
     * @param ForeignKey|string|null $upstream  The unique identifier or the name of the upstream for which to update the target.
     * @param int|null               $weight    The weight this target gets within the upstream loadbalancer (`0`-`65535`).
     */
    public function __construct(
        public ?string $target = null,
        public ?float $createdAt = null,
        public ?bool $failover = null,
        public ?string $id = null,
        public ?array $tags = null,
        public ?float $updatedAt = null,
        public ForeignKey|string|null $upstream = null,
        public ?int $weight = null,
    ) {
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
            'upstream' => $this->upstream === null ? null : ForeignKey::of($this->upstream)->toArray(),
            'weight' => $this->weight,
        ]);
    }
}
