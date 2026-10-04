<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\ResponseRateLimiting;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `response-ratelimiting` plugin (Response Rate Limiting; doc `TrafficControl/response-rate-limiting.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('TrafficControl/response-rate-limiting.md', '#/properties/config')]
final readonly class ResponseRateLimitingConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'response-ratelimiting';

    /**
     * @param bool|null                     $blockOnFirstViolation A boolean value that determines if the requests should be blocked as soon as one limit is being exceeded. Default: `false`.
     * @param bool|null                     $faultTolerant         A boolean value that determines if the requests should be proxied even if Kong has troubles connecting a thir… Default: `true`.
     * @param string|null                   $headerName            The name of the response header used to increment the counters. Default: `x-kong-limit`.
     * @param bool|null                     $hideClientHeaders     Optionally hide informative response headers. Default: `false`.
     * @param LimitBy|null                  $limitBy               The entity that will be used when aggregating the limits: `consumer`, `credential`, `ip`. Default: `consumer`.
     * @param array<array-key, Limits>|null $limits                A map that defines rate limits for the plugin.
     * @param Policy|null                   $policy                The rate-limiting policies to use for retrieving and incrementing the limits. Default: `local`.
     * @param Redis|null                    $redis                 Redis configuration
     */
    public function __construct(
        public ?bool $blockOnFirstViolation = null,
        public ?bool $faultTolerant = null,
        public ?string $headerName = null,
        public ?bool $hideClientHeaders = null,
        public ?LimitBy $limitBy = null,
        public ?array $limits = null,
        public ?Policy $policy = null,
        public ?Redis $redis = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'block_on_first_violation' => $this->blockOnFirstViolation,
            'fault_tolerant' => $this->faultTolerant,
            'header_name' => $this->headerName,
            'hide_client_headers' => $this->hideClientHeaders,
            'limit_by' => $this->limitBy?->value,
            'limits' => Data::toArrayMap($this->limits),
            'policy' => $this->policy?->value,
            'redis' => $this->redis?->toArray(),
        ]);
    }
}
