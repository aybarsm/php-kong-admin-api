<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\RateLimiting;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `rate-limiting` plugin (Rate Limiting; doc `TrafficControl/rate-limiting.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('TrafficControl/rate-limiting.md', '#/properties/config')]
final readonly class RateLimitingConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'rate-limiting';

    /**
     * @param string|null    $customKey         Overrides the computed rate-limiting key with a literal value for this request, regardless of `limit_by`.
     * @param int|float|null $day               The number of HTTP requests that can be made per day.
     * @param int|float|null $errorCode         Set a custom error code to return when the rate limit is exceeded. Default: `429`.
     * @param string|null    $errorMessage      Set a custom error message to return when the rate limit is exceeded. Default: `API rate limit exceeded`.
     * @param bool|null      $faultTolerant     A boolean value that determines if the requests should be proxied even if Kong has troubles connecting a thir… Default: `true`.
     * @param string|null    $headerName        A string representing an HTTP header name.
     * @param bool|null      $hideClientHeaders Optionally hide informative response headers. Default: `false`.
     * @param int|float|null $hour              The number of HTTP requests that can be made per hour.
     * @param LimitBy|null   $limitBy           The entity that is used when aggregating the limits. Default: `consumer`.
     * @param int|float|null $minute            The number of HTTP requests that can be made per minute.
     * @param int|float|null $month             The number of HTTP requests that can be made per month.
     * @param string|null    $path              A string representing a URL path, such as /path/to/resource.
     * @param Policy|null    $policy            The rate-limiting policies to use for retrieving and incrementing the limits. Default: `local`.
     * @param Redis|null     $redis             Redis configuration
     * @param int|float|null $second            The number of HTTP requests that can be made per second.
     * @param int|float|null $syncRate          How often to sync counter data to the central data store. Default: `-1`.
     * @param int|float|null $year              The number of HTTP requests that can be made per year.
     */
    public function __construct(
        public ?string $customKey = null,
        public int|float|null $day = null,
        public int|float|null $errorCode = null,
        public ?string $errorMessage = null,
        public ?bool $faultTolerant = null,
        public ?string $headerName = null,
        public ?bool $hideClientHeaders = null,
        public int|float|null $hour = null,
        public ?LimitBy $limitBy = null,
        public int|float|null $minute = null,
        public int|float|null $month = null,
        public ?string $path = null,
        public ?Policy $policy = null,
        public ?Redis $redis = null,
        public int|float|null $second = null,
        public int|float|null $syncRate = null,
        public int|float|null $year = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'custom_key' => $this->customKey,
            'day' => $this->day,
            'error_code' => $this->errorCode,
            'error_message' => $this->errorMessage,
            'fault_tolerant' => $this->faultTolerant,
            'header_name' => $this->headerName,
            'hide_client_headers' => $this->hideClientHeaders,
            'hour' => $this->hour,
            'limit_by' => $this->limitBy?->value,
            'minute' => $this->minute,
            'month' => $this->month,
            'path' => $this->path,
            'policy' => $this->policy?->value,
            'redis' => $this->redis?->toArray(),
            'second' => $this->second,
            'sync_rate' => $this->syncRate,
            'year' => $this->year,
        ]);
    }
}
