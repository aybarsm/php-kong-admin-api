<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\ProxyCache;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `proxy-cache` plugin (Proxy Cache; doc `TrafficControl/proxy-cache.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('TrafficControl/proxy-cache.md', '#/properties/config')]
final readonly class ProxyCacheConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'proxy-cache';

    /**
     * @param Strategy|null            $strategy         The backing data store in which to hold cache entities. Required by the plugin doc.
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
        public ?Strategy $strategy = null,
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
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'strategy' => $this->strategy?->value,
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
}
