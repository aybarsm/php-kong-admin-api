<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\HmacAuth;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `hmac-auth` plugin (HMAC Auth; doc `Authentication/hmac-auth.md`).
 *
 * Read it from a returned Plugin with `HmacAuthConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('Authentication/hmac-auth.md', '#/properties/config')]
final readonly class HmacAuthConfig implements PluginConfig
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'hmac-auth';

    /**
     * @param list<HmacAuthAlgorithms>|null $algorithms          A list of HMAC digest algorithms that the user wants to support. Default: `["hmac-sha224", "hmac-sha256", "hmac-sha384", "hmac-sha512"]`.
     * @param string|null                   $anonymous           An optional string (Consumer UUID or username) value to use as an “anonymous” consumer if authentication fail…
     * @param float|null                    $clockSkew           Clock skew in seconds to prevent replay attacks. Default: `300`.
     * @param list<string>|null             $enforceHeaders      A list of headers that the client should at least use for HTTP signature creation. Default: `[]`.
     * @param bool|null                     $hideCredentials     An optional boolean value telling the plugin to show or hide the credential from the upstream service. Default: `true`.
     * @param string|null                   $realm               When authentication fails the plugin sends `WWW-Authenticate` header with `realm` attribute value.
     * @param bool|null                     $validateRequestBody A boolean value telling the plugin to enable body validation. Default: `false`.
     */
    public function __construct(
        public ?array $algorithms = null,
        public ?string $anonymous = null,
        public ?float $clockSkew = null,
        public ?array $enforceHeaders = null,
        public ?bool $hideCredentials = null,
        public ?string $realm = null,
        public ?bool $validateRequestBody = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            algorithms: Data::enumListOrNull($data, 'algorithms', HmacAuthAlgorithms::class),
            anonymous: Data::stringOrNull($data, 'anonymous'),
            clockSkew: Data::floatOrNull($data, 'clock_skew'),
            enforceHeaders: Data::stringListOrNull($data, 'enforce_headers'),
            hideCredentials: Data::boolOrNull($data, 'hide_credentials'),
            realm: Data::stringOrNull($data, 'realm'),
            validateRequestBody: Data::boolOrNull($data, 'validate_request_body'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'algorithms' => Data::enumValues($this->algorithms),
            'anonymous' => $this->anonymous,
            'clock_skew' => $this->clockSkew,
            'enforce_headers' => $this->enforceHeaders,
            'hide_credentials' => $this->hideCredentials,
            'realm' => $this->realm,
            'validate_request_body' => $this->validateRequestBody,
        ]);
    }

    /**
     * Reads the typed configuration of a `hmac-auth` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `hmac-auth` plugin
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
