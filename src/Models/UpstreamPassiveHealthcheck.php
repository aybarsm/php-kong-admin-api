<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Enums\HealthcheckType;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `healthchecks.passive` object of Upstream (spec `Upstream.healthchecks.passive`).
 */
#[Schema('#/components/schemas/Upstream/properties/healthchecks/properties/passive')]
final readonly class UpstreamPassiveHealthcheck implements Model
{
    /**
     * @param UpstreamPassiveHealthy|null   $healthy
     * @param HealthcheckType|null          $type
     * @param UpstreamPassiveUnhealthy|null $unhealthy
     */
    public function __construct(
        public ?UpstreamPassiveHealthy $healthy = null,
        public ?HealthcheckType $type = null,
        public ?UpstreamPassiveUnhealthy $unhealthy = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $healthy = Data::mapOrNull($data, 'healthy');
        $unhealthy = Data::mapOrNull($data, 'unhealthy');

        return new self(
            healthy: $healthy === null ? null : UpstreamPassiveHealthy::fromArray($healthy),
            type: Data::enumOrNull($data, 'type', HealthcheckType::class),
            unhealthy: $unhealthy === null ? null : UpstreamPassiveUnhealthy::fromArray($unhealthy),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'healthy' => $this->healthy?->toArray(),
            'type' => $this->type?->value,
            'unhealthy' => $this->unhealthy?->toArray(),
        ]);
    }
}
