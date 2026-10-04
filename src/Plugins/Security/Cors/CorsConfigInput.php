<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Security\Cors;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `cors` plugin (CORS; doc `Security/cors.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('Security/cors.md', '#/properties/config')]
final readonly class CorsConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'cors';

    /**
     * @param bool|null          $allowOriginAbsent A boolean value that skip cors response headers when origin header of request is empty Default: `true`.
     * @param bool|null          $credentials       Flag to determine whether the `Access-Control-Allow-Credentials` header should be sent with `true` as the val… Default: `false`.
     * @param list<string>|null  $exposedHeaders    Value for the `Access-Control-Expose-Headers` header.
     * @param list<string>|null  $headers           Value for the `Access-Control-Allow-Headers` header.
     * @param int|float|null     $maxAge            Indicates how long the results of the preflight request can be cached, in `seconds`.
     * @param list<Methods>|null $methods           'Value for the `Access-Control-Allow-Methods` header. Default: `["CONNECT", "DELETE", "GET", "HEAD", "OPTIONS", "PATCH", "POST", "PUT", "TRACE"]`.
     * @param list<string>|null  $origins           List of allowed domains for the `Access-Control-Allow-Origin` header.
     * @param bool|null          $preflightContinue A boolean value that instructs the plugin to proxy the `OPTIONS` preflight request to the Upstream service. Default: `false`.
     * @param bool|null          $privateNetwork    Flag to determine whether the `Access-Control-Allow-Private-Network` header should be sent with `true` as the… Default: `false`.
     */
    public function __construct(
        public ?bool $allowOriginAbsent = null,
        public ?bool $credentials = null,
        public ?array $exposedHeaders = null,
        public ?array $headers = null,
        public int|float|null $maxAge = null,
        public ?array $methods = null,
        public ?array $origins = null,
        public ?bool $preflightContinue = null,
        public ?bool $privateNetwork = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'allow_origin_absent' => $this->allowOriginAbsent,
            'credentials' => $this->credentials,
            'exposed_headers' => $this->exposedHeaders,
            'headers' => $this->headers,
            'max_age' => $this->maxAge,
            'methods' => Data::enumValues($this->methods),
            'origins' => $this->origins,
            'preflight_continue' => $this->preflightContinue,
            'private_network' => $this->privateNetwork,
        ]);
    }
}
