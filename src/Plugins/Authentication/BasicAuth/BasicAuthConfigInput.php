<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\BasicAuth;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `basic-auth` plugin (Basic Auth; doc `Authentication/basic-auth.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('Authentication/basic-auth.md', '#/properties/config')]
final readonly class BasicAuthConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'basic-auth';

    /**
     * @param string|null               $anonymous            An optional string (Consumer UUID or username) value to use as an "anonymous" consumer if authentication fail…
     * @param BruteForceProtection|null $bruteForceProtection
     * @param bool|null                 $hideCredentials      An optional boolean value telling the plugin to show or hide the credential from the upstream service. Default: `true`.
     * @param Principals|null           $principals
     * @param string|null               $realm                When authentication fails the plugin sends `WWW-Authenticate` header with `realm` attribute value. Default: `service`.
     */
    public function __construct(
        public ?string $anonymous = null,
        public ?BruteForceProtection $bruteForceProtection = null,
        public ?bool $hideCredentials = null,
        public ?Principals $principals = null,
        public ?string $realm = null,
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
            'brute_force_protection' => $this->bruteForceProtection?->toArray(),
            'hide_credentials' => $this->hideCredentials,
            'principals' => $this->principals?->toArray(),
            'realm' => $this->realm,
        ]);
    }
}
