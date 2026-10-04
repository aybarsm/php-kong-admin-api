<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `stats.timers.stats` object of Timers (spec `#/components/responses/GetTimersDebugInfoResponse/content/application~1json/schema.stats.timers.stats`).
 */
#[Schema('#/components/responses/GetTimersDebugInfoResponse/content/application~1json/schema/properties/stats/properties/timers/additionalProperties/properties/stats')]
final readonly class TimersTimerStats implements Model
{
    /**
     * @param TimersTimerElapsedTime|null $elapsedTime
     * @param int|null                    $finish      Number of times the timer finished.
     * @param string|null                 $lastErrMsg  Last error message for the timer, if any.
     * @param int|null                    $runs        Number of runs for the timer.
     */
    public function __construct(
        public ?TimersTimerElapsedTime $elapsedTime = null,
        public ?int $finish = null,
        public ?string $lastErrMsg = null,
        public ?int $runs = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $elapsedTime = Data::mapOrNull($data, 'elapsed_time');

        return new self(
            elapsedTime: $elapsedTime === null ? null : TimersTimerElapsedTime::fromArray($elapsedTime),
            finish: Data::intOrNull($data, 'finish'),
            lastErrMsg: Data::stringOrNull($data, 'last_err_msg'),
            runs: Data::intOrNull($data, 'runs'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'elapsed_time' => $this->elapsedTime?->toArray(),
            'finish' => $this->finish,
            'last_err_msg' => $this->lastErrMsg,
            'runs' => $this->runs,
        ]);
    }
}
