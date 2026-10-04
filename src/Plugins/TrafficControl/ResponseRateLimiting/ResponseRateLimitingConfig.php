<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\ResponseRateLimiting;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `response-ratelimiting` plugin (Response Rate Limiting; doc `TrafficControl/response-rate-limiting.md`).
 *
 * Read it from a returned Plugin with `ResponseRateLimitingConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('TrafficControl/response-rate-limiting.md', '#/properties/config')]
final readonly class ResponseRateLimitingConfig implements PluginConfig
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
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $limits = Data::mapOfMapsOrNull($data, 'limits');
        $redis = Data::mapOrNull($data, 'redis');

        return new self(
            blockOnFirstViolation: Data::boolOrNull($data, 'block_on_first_violation'),
            faultTolerant: Data::boolOrNull($data, 'fault_tolerant'),
            headerName: Data::stringOrNull($data, 'header_name'),
            hideClientHeaders: Data::boolOrNull($data, 'hide_client_headers'),
            limitBy: Data::enumOrNull($data, 'limit_by', LimitBy::class),
            limits: $limits === null ? null : array_map(Limits::fromArray(...), $limits),
            policy: Data::enumOrNull($data, 'policy', Policy::class),
            redis: $redis === null ? null : Redis::fromArray($redis),
        );
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

    /**
     * Reads the typed configuration of a `response-ratelimiting` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `response-ratelimiting` plugin
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
