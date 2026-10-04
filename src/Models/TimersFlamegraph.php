<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `stats.flamegraph` object of Timers (spec `#/components/responses/GetTimersDebugInfoResponse/content/application~1json/schema.stats.flamegraph`).
 */
#[Schema('#/components/responses/GetTimersDebugInfoResponse/content/application~1json/schema/properties/stats/properties/flamegraph')]
final readonly class TimersFlamegraph implements Model
{
    /**
     * @param string|null $elapsedTime The elapsed time for the flamegraph.
     * @param string|null $pending     The number of pending timers for the flamegraph.
     * @param string|null $running     The number of running timers for the flamegraph.
     */
    public function __construct(
        public ?string $elapsedTime = null,
        public ?string $pending = null,
        public ?string $running = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            elapsedTime: Data::stringOrNull($data, 'elapsed_time'),
            pending: Data::stringOrNull($data, 'pending'),
            running: Data::stringOrNull($data, 'running'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'elapsed_time' => $this->elapsedTime,
            'pending' => $this->pending,
            'running' => $this->running,
        ]);
    }
}
