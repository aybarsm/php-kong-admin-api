<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `healthchecks.active.healthy` object of Upstream (spec `Upstream.healthchecks.active.healthy`).
 */
#[Schema('#/components/schemas/Upstream/properties/healthchecks/properties/active/properties/healthy')]
final readonly class UpstreamActiveHealthy implements Model
{
    /**
     * @param list<int>|null $httpStatuses
     * @param float|null     $interval
     * @param int|null       $successes
     */
    public function __construct(
        public ?array $httpStatuses = null,
        public ?float $interval = null,
        public ?int $successes = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            httpStatuses: Data::intListOrNull($data, 'http_statuses'),
            interval: Data::floatOrNull($data, 'interval'),
            successes: Data::intOrNull($data, 'successes'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'http_statuses' => $this->httpStatuses,
            'interval' => $this->interval,
            'successes' => $this->successes,
        ]);
    }
}
