<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\KeyAuth;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `key-auth` plugin (Key Auth; doc `Authentication/key-auth.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('Authentication/key-auth.md', '#/properties/config')]
final readonly class KeyAuthConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'key-auth';

    /**
     * @param string|null               $anonymous       An optional string (consumer UUID or username) value to use as an “anonymous” consumer if authentication fail…
     * @param bool|null                 $hideCredentials An optional boolean value telling the plugin to show or hide the credential from the upstream service. Default: `true`.
     * @param list<IdentityRealms>|null $identityRealms  A configuration of Konnect Identity Realms that indicate where to source a consumer from.
     * @param bool|null                 $keyInBody       If enabled, the plugin reads the request body. Default: `false`.
     * @param bool|null                 $keyInHeader     If enabled (default), the plugin reads the request header and tries to find the key in it. Default: `true`.
     * @param bool|null                 $keyInQuery      If enabled (default), the plugin reads the query parameter in the request and tries to find the key in it. Default: `true`.
     * @param list<string>|null         $keyNames        Describes an array of parameter names where the plugin will look for a key. Default: `["apikey"]`.
     * @param Principals|null           $principals
     * @param string|null               $realm           When authentication fails the plugin sends `WWW-Authenticate` header with `realm` attribute value.
     * @param bool|null                 $runOnPreflight  A boolean value that indicates whether the plugin should run (and try to authenticate) on `OPTIONS` preflight… Default: `true`.
     */
    public function __construct(
        public ?string $anonymous = null,
        public ?bool $hideCredentials = null,
        public ?array $identityRealms = null,
        public ?bool $keyInBody = null,
        public ?bool $keyInHeader = null,
        public ?bool $keyInQuery = null,
        public ?array $keyNames = null,
        public ?Principals $principals = null,
        public ?string $realm = null,
        public ?bool $runOnPreflight = null,
    ) {
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
}
