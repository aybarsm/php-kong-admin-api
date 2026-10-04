<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\KeyAuth;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `key-auth` plugin (Key Auth; doc `Authentication/key-auth.md`).
 *
 * Read it from a returned Plugin with `KeyAuthConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('Authentication/key-auth.md', '#/properties/config')]
final readonly class KeyAuthConfig implements PluginConfig
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'key-auth';

    /**
     * @param string|null                      $anonymous       An optional string (consumer UUID or username) value to use as an “anonymous” consumer if authentication fail…
     * @param bool|null                        $hideCredentials An optional boolean value telling the plugin to show or hide the credential from the upstream service. Default: `true`.
     * @param list<KeyAuthIdentityRealms>|null $identityRealms  A configuration of Konnect Identity Realms that indicate where to source a consumer from.
     * @param bool|null                        $keyInBody       If enabled, the plugin reads the request body. Default: `false`.
     * @param bool|null                        $keyInHeader     If enabled (default), the plugin reads the request header and tries to find the key in it. Default: `true`.
     * @param bool|null                        $keyInQuery      If enabled (default), the plugin reads the query parameter in the request and tries to find the key in it. Default: `true`.
     * @param list<string>|null                $keyNames        Describes an array of parameter names where the plugin will look for a key. Default: `["apikey"]`.
     * @param KeyAuthPrincipals|null           $principals
     * @param string|null                      $realm           When authentication fails the plugin sends `WWW-Authenticate` header with `realm` attribute value.
     * @param bool|null                        $runOnPreflight  A boolean value that indicates whether the plugin should run (and try to authenticate) on `OPTIONS` preflight… Default: `true`.
     */
    public function __construct(
        public ?string $anonymous = null,
        public ?bool $hideCredentials = null,
        public ?array $identityRealms = null,
        public ?bool $keyInBody = null,
        public ?bool $keyInHeader = null,
        public ?bool $keyInQuery = null,
        public ?array $keyNames = null,
        public ?KeyAuthPrincipals $principals = null,
        public ?string $realm = null,
        public ?bool $runOnPreflight = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $identityRealms = Data::listOfMapsOrNull($data, 'identity_realms');
        $principals = Data::mapOrNull($data, 'principals');

        return new self(
            anonymous: Data::stringOrNull($data, 'anonymous'),
            hideCredentials: Data::boolOrNull($data, 'hide_credentials'),
            identityRealms: $identityRealms === null ? null : array_map(KeyAuthIdentityRealms::fromArray(...), $identityRealms),
            keyInBody: Data::boolOrNull($data, 'key_in_body'),
            keyInHeader: Data::boolOrNull($data, 'key_in_header'),
            keyInQuery: Data::boolOrNull($data, 'key_in_query'),
            keyNames: Data::stringListOrNull($data, 'key_names'),
            principals: $principals === null ? null : KeyAuthPrincipals::fromArray($principals),
            realm: Data::stringOrNull($data, 'realm'),
            runOnPreflight: Data::boolOrNull($data, 'run_on_preflight'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'anonymous' => $this->anonymous,
            'hide_credentials' => $this->hideCredentials,
            'identity_realms' => Data::toArrays($this->identityRealms),
            'key_in_body' => $this->keyInBody,
            'key_in_header' => $this->keyInHeader,
            'key_in_query' => $this->keyInQuery,
            'key_names' => $this->keyNames,
            'principals' => $this->principals?->toArray(),
            'realm' => $this->realm,
            'run_on_preflight' => $this->runOnPreflight,
        ]);
    }

    /**
     * Reads the typed configuration of a `key-auth` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `key-auth` plugin
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
