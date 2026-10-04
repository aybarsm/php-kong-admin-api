<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `stats` object of Timers (spec `#/components/responses/GetTimersDebugInfoResponse/content/application~1json/schema.stats`).
 */
#[Schema('#/components/responses/GetTimersDebugInfoResponse/content/application~1json/schema/properties/stats')]
final readonly class TimersStats implements Model
{
    /**
     * @param TimersFlamegraph|null              $flamegraph String-encoded timer-related flamegraph data.
     * @param TimersSys|null                     $sys        List of the number of different types of timers.
     * @param array<array-key, TimersTimer>|null $timers     Timer statistics for the worker.
     */
    public function __construct(
        public ?TimersFlamegraph $flamegraph = null,
        public ?TimersSys $sys = null,
        public ?array $timers = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $flamegraph = Data::mapOrNull($data, 'flamegraph');
        $sys = Data::mapOrNull($data, 'sys');
        $timers = Data::mapOfMapsOrNull($data, 'timers');

        return new self(
            flamegraph: $flamegraph === null ? null : TimersFlamegraph::fromArray($flamegraph),
            sys: $sys === null ? null : TimersSys::fromArray($sys),
            timers: $timers === null ? null : array_map(TimersTimer::fromArray(...), $timers),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'flamegraph' => $this->flamegraph?->toArray(),
            'sys' => $this->sys?->toArray(),
            'timers' => Data::toArrayMap($this->timers),
        ]);
    }
}
