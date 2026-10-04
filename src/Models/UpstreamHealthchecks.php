<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `healthchecks` object of Upstream (spec `Upstream.healthchecks`).
 */
#[Schema('#/components/schemas/Upstream/properties/healthchecks')]
final readonly class UpstreamHealthchecks implements Model
{
    /**
     * @param UpstreamActiveHealthcheck|null  $active
     * @param UpstreamPassiveHealthcheck|null $passive
     * @param float|null                      $threshold
     */
    public function __construct(
        public ?UpstreamActiveHealthcheck $active = null,
        public ?UpstreamPassiveHealthcheck $passive = null,
        public ?float $threshold = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $active = Data::mapOrNull($data, 'active');
        $passive = Data::mapOrNull($data, 'passive');

        return new self(
            active: $active === null ? null : UpstreamActiveHealthcheck::fromArray($active),
            passive: $passive === null ? null : UpstreamPassiveHealthcheck::fromArray($passive),
            threshold: Data::floatOrNull($data, 'threshold'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'active' => $this->active?->toArray(),
            'passive' => $this->passive?->toArray(),
            'threshold' => $this->threshold,
        ]);
    }
}
