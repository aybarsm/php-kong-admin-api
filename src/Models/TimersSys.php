<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `stats.sys` object of Timers (spec `#/components/responses/GetTimersDebugInfoResponse/content/application~1json/schema.stats.sys`).
 */
#[Schema('#/components/responses/GetTimersDebugInfoResponse/content/application~1json/schema/properties/stats/properties/sys')]
final readonly class TimersSys implements Model
{
    /**
     * @param int|null $pending The number of pending timers.
     * @param int|null $running The number of running timers.
     * @param int|null $runs    The total number of runs for the timers.
     * @param int|null $total   The total number of timers (running + pending + waiting).
     * @param int|null $waiting The number of unexpired timers.
     */
    public function __construct(
        public ?int $pending = null,
        public ?int $running = null,
        public ?int $runs = null,
        public ?int $total = null,
        public ?int $waiting = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            pending: Data::intOrNull($data, 'pending'),
            running: Data::intOrNull($data, 'running'),
            runs: Data::intOrNull($data, 'runs'),
            total: Data::intOrNull($data, 'total'),
            waiting: Data::intOrNull($data, 'waiting'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'pending' => $this->pending,
            'running' => $this->running,
            'runs' => $this->runs,
            'total' => $this->total,
            'waiting' => $this->waiting,
        ]);
    }
}
