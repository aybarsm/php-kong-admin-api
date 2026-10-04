<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `stats.timers{}` object of Timers (spec `#/components/responses/GetTimersDebugInfoResponse/content/application~1json/schema.stats.timers{}`).
 */
#[Schema('#/components/responses/GetTimersDebugInfoResponse/content/application~1json/schema/properties/stats/properties/timers/additionalProperties')]
final readonly class TimersTimer implements Model
{
    /**
     * @param bool|null             $isRunning Whether the timer is currently running.
     * @param TimersTimerMeta|null  $meta      Metadata about the timer.
     * @param string|null           $name      The name of the timer.
     * @param TimersTimerStats|null $stats     Stats related to the timer.
     */
    public function __construct(
        public ?bool $isRunning = null,
        public ?TimersTimerMeta $meta = null,
        public ?string $name = null,
        public ?TimersTimerStats $stats = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $meta = Data::mapOrNull($data, 'meta');
        $stats = Data::mapOrNull($data, 'stats');

        return new self(
            isRunning: Data::boolOrNull($data, 'is_running'),
            meta: $meta === null ? null : TimersTimerMeta::fromArray($meta),
            name: Data::stringOrNull($data, 'name'),
            stats: $stats === null ? null : TimersTimerStats::fromArray($stats),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'is_running' => $this->isRunning,
            'meta' => $this->meta?->toArray(),
            'name' => $this->name,
            'stats' => $this->stats?->toArray(),
        ]);
    }
}
