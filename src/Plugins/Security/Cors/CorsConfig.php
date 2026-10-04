<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Security\Cors;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `cors` plugin (CORS; doc `Security/cors.md`).
 *
 * Read it from a returned Plugin with `CorsConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('Security/cors.md', '#/properties/config')]
final readonly class CorsConfig implements PluginConfig
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'cors';

    /**
     * @param bool|null              $allowOriginAbsent A boolean value that skip cors response headers when origin header of request is empty Default: `true`.
     * @param bool|null              $credentials       Flag to determine whether the `Access-Control-Allow-Credentials` header should be sent with `true` as the val… Default: `false`.
     * @param list<string>|null      $exposedHeaders    Value for the `Access-Control-Expose-Headers` header.
     * @param list<string>|null      $headers           Value for the `Access-Control-Allow-Headers` header.
     * @param float|null             $maxAge            Indicates how long the results of the preflight request can be cached, in `seconds`.
     * @param list<CorsMethods>|null $methods           'Value for the `Access-Control-Allow-Methods` header. Default: `["CONNECT", "DELETE", "GET", "HEAD", "OPTIONS", "PATCH", "POST", "PUT", "TRACE"]`.
     * @param list<string>|null      $origins           List of allowed domains for the `Access-Control-Allow-Origin` header.
     * @param bool|null              $preflightContinue A boolean value that instructs the plugin to proxy the `OPTIONS` preflight request to the Upstream service. Default: `false`.
     * @param bool|null              $privateNetwork    Flag to determine whether the `Access-Control-Allow-Private-Network` header should be sent with `true` as the… Default: `false`.
     */
    public function __construct(
        public ?bool $allowOriginAbsent = null,
        public ?bool $credentials = null,
        public ?array $exposedHeaders = null,
        public ?array $headers = null,
        public ?float $maxAge = null,
        public ?array $methods = null,
        public ?array $origins = null,
        public ?bool $preflightContinue = null,
        public ?bool $privateNetwork = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            allowOriginAbsent: Data::boolOrNull($data, 'allow_origin_absent'),
            credentials: Data::boolOrNull($data, 'credentials'),
            exposedHeaders: Data::stringListOrNull($data, 'exposed_headers'),
            headers: Data::stringListOrNull($data, 'headers'),
            maxAge: Data::floatOrNull($data, 'max_age'),
            methods: Data::enumListOrNull($data, 'methods', CorsMethods::class),
            origins: Data::stringListOrNull($data, 'origins'),
            preflightContinue: Data::boolOrNull($data, 'preflight_continue'),
            privateNetwork: Data::boolOrNull($data, 'private_network'),
        );
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

    /**
     * Reads the typed configuration of a `cors` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `cors` plugin
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
