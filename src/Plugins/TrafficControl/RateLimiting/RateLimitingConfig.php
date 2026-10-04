<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\RateLimiting;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `rate-limiting` plugin (Rate Limiting; doc `TrafficControl/rate-limiting.md`).
 *
 * Read it from a returned Plugin with `RateLimitingConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('TrafficControl/rate-limiting.md', '#/properties/config')]
final readonly class RateLimitingConfig implements PluginConfig
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'rate-limiting';

    /**
     * @param string|null              $customKey         Overrides the computed rate-limiting key with a literal value for this request, regardless of `limit_by`.
     * @param float|null               $day               The number of HTTP requests that can be made per day.
     * @param float|null               $errorCode         Set a custom error code to return when the rate limit is exceeded. Default: `429`.
     * @param string|null              $errorMessage      Set a custom error message to return when the rate limit is exceeded. Default: `API rate limit exceeded`.
     * @param bool|null                $faultTolerant     A boolean value that determines if the requests should be proxied even if Kong has troubles connecting a thir… Default: `true`.
     * @param string|null              $headerName        A string representing an HTTP header name.
     * @param bool|null                $hideClientHeaders Optionally hide informative response headers. Default: `false`.
     * @param float|null               $hour              The number of HTTP requests that can be made per hour.
     * @param RateLimitingLimitBy|null $limitBy           The entity that is used when aggregating the limits. Default: `consumer`.
     * @param float|null               $minute            The number of HTTP requests that can be made per minute.
     * @param float|null               $month             The number of HTTP requests that can be made per month.
     * @param string|null              $path              A string representing a URL path, such as /path/to/resource.
     * @param RateLimitingPolicy|null  $policy            The rate-limiting policies to use for retrieving and incrementing the limits. Default: `local`.
     * @param RateLimitingRedis|null   $redis             Redis configuration
     * @param float|null               $second            The number of HTTP requests that can be made per second.
     * @param float|null               $syncRate          How often to sync counter data to the central data store. Default: `-1`.
     * @param float|null               $year              The number of HTTP requests that can be made per year.
     */
    public function __construct(
        public ?string $customKey = null,
        public ?float $day = null,
        public ?float $errorCode = null,
        public ?string $errorMessage = null,
        public ?bool $faultTolerant = null,
        public ?string $headerName = null,
        public ?bool $hideClientHeaders = null,
        public ?float $hour = null,
        public ?RateLimitingLimitBy $limitBy = null,
        public ?float $minute = null,
        public ?float $month = null,
        public ?string $path = null,
        public ?RateLimitingPolicy $policy = null,
        public ?RateLimitingRedis $redis = null,
        public ?float $second = null,
        public ?float $syncRate = null,
        public ?float $year = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $redis = Data::mapOrNull($data, 'redis');

        return new self(
            customKey: Data::stringOrNull($data, 'custom_key'),
            day: Data::floatOrNull($data, 'day'),
            errorCode: Data::floatOrNull($data, 'error_code'),
            errorMessage: Data::stringOrNull($data, 'error_message'),
            faultTolerant: Data::boolOrNull($data, 'fault_tolerant'),
            headerName: Data::stringOrNull($data, 'header_name'),
            hideClientHeaders: Data::boolOrNull($data, 'hide_client_headers'),
            hour: Data::floatOrNull($data, 'hour'),
            limitBy: Data::enumOrNull($data, 'limit_by', RateLimitingLimitBy::class),
            minute: Data::floatOrNull($data, 'minute'),
            month: Data::floatOrNull($data, 'month'),
            path: Data::stringOrNull($data, 'path'),
            policy: Data::enumOrNull($data, 'policy', RateLimitingPolicy::class),
            redis: $redis === null ? null : RateLimitingRedis::fromArray($redis),
            second: Data::floatOrNull($data, 'second'),
            syncRate: Data::floatOrNull($data, 'sync_rate'),
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

    /**
     * Reads the typed configuration of a `rate-limiting` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `rate-limiting` plugin
     * @throws UnexpectedResponseException when its `config` does not match the plugin doc
     */
    #[Override]
    public static function fromPlugin(Plugin $plugin): static
    {
        if ($plugin->name !== self::NAME) {
            throw new InvalidArgumentException(sprintf('Expected a "%s" plugin, got "%s".', self::NAME, $plugin->name));
        }

        return self::fromArray(Data::asMap($plugin->config ?? [], 'config'));
    }
}
