<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\ProxyCache;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `proxy-cache` plugin (Proxy Cache; doc `TrafficControl/proxy-cache.md`).
 *
 * Read it from a returned Plugin with `ProxyCacheConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('TrafficControl/proxy-cache.md', '#/properties/config')]
final readonly class ProxyCacheConfig implements PluginConfig
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'proxy-cache';

    /**
     * @param Strategy                 $strategy         The backing data store in which to hold cache entities.
     * @param bool|null                $cacheByPrincipal When enabled, use the authenticated Principal's UUID to compose the cache key. Default: `false`.
     * @param bool|null                $cacheControl     When enabled, respect the Cache-Control behaviors defined in RFC7234. Default: `false`.
     * @param int|null                 $cacheTtl         TTL, in seconds, of cache entities. Default: `300`.
     * @param list<string>|null        $contentType      Upstream response content types considered cacheable. Default: `["application/json", "text/plain"]`.
     * @param bool|null                $ignoreUriCase    Default: `false`.
     * @param Memory|null              $memory
     * @param list<RequestMethod>|null $requestMethod    Downstream request methods considered cacheable. Default: `["GET", "HEAD"]`.
     * @param list<int>|null           $responseCode     Upstream response status code considered cacheable. Default: `[200, 301, 404]`.
     * @param ResponseHeaders|null     $responseHeaders  Caching related diagnostic headers that should be included in cached responses
     * @param int|null                 $storageTtl       Number of seconds to keep resources in the storage backend.
     * @param list<string>|null        $varyHeaders      Relevant headers considered for the cache key.
     * @param list<string>|null        $varyQueryParams  Relevant query parameters considered for the cache key.
     */
    public function __construct(
        public Strategy $strategy,
        public ?bool $cacheByPrincipal = null,
        public ?bool $cacheControl = null,
        public ?int $cacheTtl = null,
        public ?array $contentType = null,
        public ?bool $ignoreUriCase = null,
        public ?Memory $memory = null,
        public ?array $requestMethod = null,
        public ?array $responseCode = null,
        public ?ResponseHeaders $responseHeaders = null,
        public ?int $storageTtl = null,
        public ?array $varyHeaders = null,
        public ?array $varyQueryParams = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $memory = Data::mapOrNull($data, 'memory');
        $responseHeaders = Data::mapOrNull($data, 'response_headers');

        return new self(
            strategy: Data::enum($data, 'strategy', Strategy::class),
            cacheByPrincipal: Data::boolOrNull($data, 'cache_by_principal'),
            cacheControl: Data::boolOrNull($data, 'cache_control'),
            cacheTtl: Data::intOrNull($data, 'cache_ttl'),
            contentType: Data::stringListOrNull($data, 'content_type'),
            ignoreUriCase: Data::boolOrNull($data, 'ignore_uri_case'),
            memory: $memory === null ? null : Memory::fromArray($memory),
            requestMethod: Data::enumListOrNull($data, 'request_method', RequestMethod::class),
            responseCode: Data::intListOrNull($data, 'response_code'),
            responseHeaders: $responseHeaders === null ? null : ResponseHeaders::fromArray($responseHeaders),
            storageTtl: Data::intOrNull($data, 'storage_ttl'),
            varyHeaders: Data::stringListOrNull($data, 'vary_headers'),
            varyQueryParams: Data::stringListOrNull($data, 'vary_query_params'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'strategy' => $this->strategy->value,
            'cache_by_principal' => $this->cacheByPrincipal,
            'cache_control' => $this->cacheControl,
            'cache_ttl' => $this->cacheTtl,
            'content_type' => $this->contentType,
            'ignore_uri_case' => $this->ignoreUriCase,
            'memory' => $this->memory?->toArray(),
            'request_method' => Data::enumValues($this->requestMethod),
            'response_code' => $this->responseCode,
            'response_headers' => $this->responseHeaders?->toArray(),
            'storage_ttl' => $this->storageTtl,
            'vary_headers' => $this->varyHeaders,
            'vary_query_params' => $this->varyQueryParams,
        ]);
    }

    /**
     * Reads the typed configuration of a `proxy-cache` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `proxy-cache` plugin
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
