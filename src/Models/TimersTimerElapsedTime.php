<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `stats.timers.stats.elapsed_time` object of Timers (spec `#/components/responses/GetTimersDebugInfoResponse/content/application~1json/schema.stats.timers.stats.elapsed_time`).
 */
#[Schema('#/components/responses/GetTimersDebugInfoResponse/content/application~1json/schema/properties/stats/properties/timers/additionalProperties/properties/stats/properties/elapsed_time')]
final readonly class TimersTimerElapsedTime implements Model
{
    /**
     * @param float|null $avg      Average elapsed time.
     * @param float|null $max      Maximum elapsed time.
     * @param float|null $min      Minimum elapsed time.
     * @param float|null $variance Variance of the elapsed time.
     */
    public function __construct(
        public ?float $avg = null,
        public ?float $max = null,
        public ?float $min = null,
        public ?float $variance = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            avg: Data::floatOrNull($data, 'avg'),
            max: Data::floatOrNull($data, 'max'),
            min: Data::floatOrNull($data, 'min'),
            variance: Data::floatOrNull($data, 'variance'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'avg' => $this->avg,
            'max' => $this->max,
            'min' => $this->min,
            'variance' => $this->variance,
        ]);
    }
}
