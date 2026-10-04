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
final readonly class ResponseRateLimitingLimits implements Model
{
    /**
     * @param float|null $day
     * @param float|null $hour
     * @param float|null $minute
     * @param float|null $month
     * @param float|null $second
     * @param float|null $year
     */
    public function __construct(
        public ?float $day = null,
        public ?float $hour = null,
        public ?float $minute = null,
        public ?float $month = null,
        public ?float $second = null,
        public ?float $year = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            day: Data::floatOrNull($data, 'day'),
            hour: Data::floatOrNull($data, 'hour'),
            minute: Data::floatOrNull($data, 'minute'),
            month: Data::floatOrNull($data, 'month'),
            second: Data::floatOrNull($data, 'second'),
            year: Data::floatOrNull($data, 'year'),
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
