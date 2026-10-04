<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\LdapAuth;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `ldap-auth` plugin (LDAP Authentication; doc `Authentication/ldap-auth.md`).
 *
 * Read it from a returned Plugin with `LdapAuthConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('Authentication/ldap-auth.md', '#/properties/config')]
final readonly class LdapAuthConfig implements PluginConfig
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'ldap-auth';

    /**
     * @param string      $attribute       Attribute to be used to search the user; e.g.
     * @param string      $baseDn          Base DN as the starting point for the search; e.g., dc=example,dc=com
     * @param string      $ldapHost        A string representing a host name, such as example.com.
     * @param string|null $anonymous       An optional string (consumer UUID or username) value to use as an “anonymous” consumer if authentication fail…
     * @param float|null  $cacheTtl        Cache expiry time in seconds. Default: `60`.
     * @param string|null $headerType      An optional string to use as part of the Authorization header Default: `ldap`.
     * @param bool|null   $hideCredentials An optional boolean value telling the plugin to hide the credential to the upstream server. Default: `true`.
     * @param float|null  $keepalive       An optional value in milliseconds that defines how long an idle connection to LDAP server will live before be… Default: `60000`.
     * @param int|null    $ldapPort        An integer representing a port number between 0 and 65535, inclusive. Default: `389`.
     * @param bool|null   $ldaps           Set to `true` to connect using the LDAPS protocol (LDAP over TLS). Default: `false`.
     * @param string|null $realm           When authentication fails the plugin sends `WWW-Authenticate` header with `realm` attribute value.
     * @param bool|null   $startTls        Set it to `true` to issue StartTLS (Transport Layer Security) extended operation over `ldap` connection. Default: `false`.
     * @param float|null  $timeout         An optional timeout in milliseconds when waiting for connection with LDAP server. Default: `10000`.
     * @param bool|null   $verifyLdapHost  Set to `true` to authenticate LDAP server. Default: `true`.
     */
    public function __construct(
        public string $attribute,
        public string $baseDn,
        public string $ldapHost,
        public ?string $anonymous = null,
        public ?float $cacheTtl = null,
        public ?string $headerType = null,
        public ?bool $hideCredentials = null,
        public ?float $keepalive = null,
        public ?int $ldapPort = null,
        public ?bool $ldaps = null,
        public ?string $realm = null,
        public ?bool $startTls = null,
        public ?float $timeout = null,
        public ?bool $verifyLdapHost = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            attribute: Data::string($data, 'attribute'),
            baseDn: Data::string($data, 'base_dn'),
            ldapHost: Data::string($data, 'ldap_host'),
            anonymous: Data::stringOrNull($data, 'anonymous'),
            cacheTtl: Data::floatOrNull($data, 'cache_ttl'),
            headerType: Data::stringOrNull($data, 'header_type'),
            hideCredentials: Data::boolOrNull($data, 'hide_credentials'),
            keepalive: Data::floatOrNull($data, 'keepalive'),
            ldapPort: Data::intOrNull($data, 'ldap_port'),
            ldaps: Data::boolOrNull($data, 'ldaps'),
            realm: Data::stringOrNull($data, 'realm'),
            startTls: Data::boolOrNull($data, 'start_tls'),
            timeout: Data::floatOrNull($data, 'timeout'),
            verifyLdapHost: Data::boolOrNull($data, 'verify_ldap_host'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'attribute' => $this->attribute,
            'base_dn' => $this->baseDn,
            'ldap_host' => $this->ldapHost,
            'anonymous' => $this->anonymous,
            'cache_ttl' => $this->cacheTtl,
            'header_type' => $this->headerType,
            'hide_credentials' => $this->hideCredentials,
            'keepalive' => $this->keepalive,
            'ldap_port' => $this->ldapPort,
            'ldaps' => $this->ldaps,
            'realm' => $this->realm,
            'start_tls' => $this->startTls,
            'timeout' => $this->timeout,
            'verify_ldap_host' => $this->verifyLdapHost,
        ]);
    }

    /**
     * Reads the typed configuration of a `ldap-auth` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `ldap-auth` plugin
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
