<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\BasicAuth;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `basic-auth` plugin (Basic Auth; doc `Authentication/basic-auth.md`).
 *
 * Read it from a returned Plugin with `BasicAuthConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('Authentication/basic-auth.md', '#/properties/config')]
final readonly class BasicAuthConfig implements PluginConfig
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'basic-auth';

    /**
     * @param string|null                        $anonymous            An optional string (Consumer UUID or username) value to use as an "anonymous" consumer if authentication fail…
     * @param BasicAuthBruteForceProtection|null $bruteForceProtection
     * @param bool|null                          $hideCredentials      An optional boolean value telling the plugin to show or hide the credential from the upstream service. Default: `true`.
     * @param BasicAuthPrincipals|null           $principals
     * @param string|null                        $realm                When authentication fails the plugin sends `WWW-Authenticate` header with `realm` attribute value. Default: `service`.
     */
    public function __construct(
        public ?string $anonymous = null,
        public ?BasicAuthBruteForceProtection $bruteForceProtection = null,
        public ?bool $hideCredentials = null,
        public ?BasicAuthPrincipals $principals = null,
        public ?string $realm = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $bruteForceProtection = Data::mapOrNull($data, 'brute_force_protection');
        $principals = Data::mapOrNull($data, 'principals');

        return new self(
            anonymous: Data::stringOrNull($data, 'anonymous'),
            bruteForceProtection: $bruteForceProtection === null ? null : BasicAuthBruteForceProtection::fromArray($bruteForceProtection),
            hideCredentials: Data::boolOrNull($data, 'hide_credentials'),
            principals: $principals === null ? null : BasicAuthPrincipals::fromArray($principals),
            realm: Data::stringOrNull($data, 'realm'),
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
            'brute_force_protection' => $this->bruteForceProtection?->toArray(),
            'hide_credentials' => $this->hideCredentials,
            'principals' => $this->principals?->toArray(),
            'realm' => $this->realm,
        ]);
    }

    /**
     * Reads the typed configuration of a `basic-auth` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `basic-auth` plugin
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
