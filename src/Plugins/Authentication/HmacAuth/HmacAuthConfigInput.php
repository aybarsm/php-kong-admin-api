<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\HmacAuth;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `hmac-auth` plugin (HMAC Auth; doc `Authentication/hmac-auth.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('Authentication/hmac-auth.md', '#/properties/config')]
final readonly class HmacAuthConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'hmac-auth';

    /**
     * @param list<Algorithms>|null $algorithms          A list of HMAC digest algorithms that the user wants to support. Default: `["hmac-sha224", "hmac-sha256", "hmac-sha384", "hmac-sha512"]`.
     * @param string|null           $anonymous           An optional string (Consumer UUID or username) value to use as an “anonymous” consumer if authentication fail…
     * @param int|float|null        $clockSkew           Clock skew in seconds to prevent replay attacks. Default: `300`.
     * @param list<string>|null     $enforceHeaders      A list of headers that the client should at least use for HTTP signature creation. Default: `[]`.
     * @param bool|null             $hideCredentials     An optional boolean value telling the plugin to show or hide the credential from the upstream service. Default: `true`.
     * @param string|null           $realm               When authentication fails the plugin sends `WWW-Authenticate` header with `realm` attribute value.
     * @param bool|null             $validateRequestBody A boolean value telling the plugin to enable body validation. Default: `false`.
     */
    public function __construct(
        public ?array $algorithms = null,
        public ?string $anonymous = null,
        public int|float|null $clockSkew = null,
        public ?array $enforceHeaders = null,
        public ?bool $hideCredentials = null,
        public ?string $realm = null,
        public ?bool $validateRequestBody = null,
    ) {
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
}
