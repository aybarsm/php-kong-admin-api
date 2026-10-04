<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\ResponseRateLimiting;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.limits{}` object of the Response Rate Limiting plugin (doc `TrafficControl/response-rate-limiting.md`).
 */
#[PluginSchema('TrafficControl/response-rate-limiting.md', '#/properties/config/properties/limits/additionalProperties')]
final readonly class Limits implements Model
{
    /**
     * @param int|float|null $day
     * @param int|float|null $hour
     * @param int|float|null $minute
     * @param int|float|null $month
     * @param int|float|null $second
     * @param int|float|null $year
     */
    public function __construct(
        public int|float|null $day = null,
        public int|float|null $hour = null,
        public int|float|null $minute = null,
        public int|float|null $month = null,
        public int|float|null $second = null,
        public int|float|null $year = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            day: Data::numberOrNull($data, 'day'),
            hour: Data::numberOrNull($data, 'hour'),
            minute: Data::numberOrNull($data, 'minute'),
            month: Data::numberOrNull($data, 'month'),
            second: Data::numberOrNull($data, 'second'),
            year: Data::numberOrNull($data, 'year'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'day' => $this->day,
            'hour' => $this->hour,
            'minute' => $this->minute,
            'month' => $this->month,
            'second' => $this->second,
            'year' => $this->year,
        ]);
    }
}
