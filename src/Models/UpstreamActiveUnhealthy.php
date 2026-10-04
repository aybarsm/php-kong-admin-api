<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `healthchecks.active.unhealthy` object of Upstream (spec `Upstream.healthchecks.active.unhealthy`).
 */
#[Schema('#/components/schemas/Upstream/properties/healthchecks/properties/active/properties/unhealthy')]
final readonly class UpstreamActiveUnhealthy implements Model
{
    /**
     * @param int|null       $httpFailures
     * @param list<int>|null $httpStatuses
     * @param float|null     $interval
     * @param int|null       $tcpFailures
     * @param int|null       $timeouts
     */
    public function __construct(
        public ?int $httpFailures = null,
        public ?array $httpStatuses = null,
        public ?float $interval = null,
        public ?int $tcpFailures = null,
        public ?int $timeouts = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            httpFailures: Data::intOrNull($data, 'http_failures'),
            httpStatuses: Data::intListOrNull($data, 'http_statuses'),
            interval: Data::floatOrNull($data, 'interval'),
            tcpFailures: Data::intOrNull($data, 'tcp_failures'),
            timeouts: Data::intOrNull($data, 'timeouts'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'http_failures' => $this->httpFailures,
            'http_statuses' => $this->httpStatuses,
            'interval' => $this->interval,
            'tcp_failures' => $this->tcpFailures,
            'timeouts' => $this->timeouts,
        ]);
    }
}
