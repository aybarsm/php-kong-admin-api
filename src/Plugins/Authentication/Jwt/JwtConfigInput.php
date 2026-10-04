<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\Jwt;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `jwt` plugin (JWT; doc `Authentication/jwt.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('Authentication/jwt.md', '#/properties/config')]
final readonly class JwtConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'jwt';

    /**
     * @param string|null                  $anonymous         An optional string (consumer UUID or username) value to use as an “anonymous” consumer if authentication fail…
     * @param list<JwtClaimsToVerify>|null $claimsToVerify    A list of registered claims (according to RFC 7519) that Kong can verify as well.
     * @param list<string>|null            $cookieNames       A list of cookie names that Kong will inspect to retrieve JWTs. Default: `[]`.
     * @param list<string>|null            $headerNames       A list of HTTP header names that Kong will inspect to retrieve JWTs. Default: `["authorization"]`.
     * @param string|null                  $keyClaimName      The name of the claim in which the key identifying the secret must be passed. Default: `iss`.
     * @param float|null                   $maximumExpiration A value between 0 and 31536000 (365 days) limiting the lifetime of the JWT to maximum_expiration seconds in t… Default: `0`.
     * @param string|null                  $realm             When authentication fails the plugin sends `WWW-Authenticate` header with `realm` attribute value.
     * @param bool|null                    $runOnPreflight    A boolean value that indicates whether the plugin should run (and try to authenticate) on OPTIONS preflight r… Default: `true`.
     * @param bool|null                    $secretIsBase64    If true, the plugin assumes the credential’s secret to be base64 encoded. Default: `false`.
     * @param list<string>|null            $uriParamNames     A list of querystring parameters that Kong will inspect to retrieve JWTs. Default: `["jwt"]`.
     */
    public function __construct(
        public ?string $anonymous = null,
        public ?array $claimsToVerify = null,
        public ?array $cookieNames = null,
        public ?array $headerNames = null,
        public ?string $keyClaimName = null,
        public ?float $maximumExpiration = null,
        public ?string $realm = null,
        public ?bool $runOnPreflight = null,
        public ?bool $secretIsBase64 = null,
        public ?array $uriParamNames = null,
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
            'claims_to_verify' => Data::enumValues($this->claimsToVerify),
            'cookie_names' => $this->cookieNames,
            'header_names' => $this->headerNames,
            'key_claim_name' => $this->keyClaimName,
            'maximum_expiration' => $this->maximumExpiration,
            'realm' => $this->realm,
            'run_on_preflight' => $this->runOnPreflight,
            'secret_is_base64' => $this->secretIsBase64,
            'uri_param_names' => $this->uriParamNames,
        ]);
    }
}
