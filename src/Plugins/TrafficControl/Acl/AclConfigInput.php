<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\Acl;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `acl` plugin (ACL; doc `TrafficControl/acl.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('TrafficControl/acl.md', '#/properties/config')]
final readonly class AclConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'acl';

    /**
     * @param list<string>|null $allow                        Arbitrary group names that are allowed to consume the service or route.
     * @param list<string>|null $allowWhen                    Allow the request if it matches any of these CEL boolean expressions evaluated against the request context (c…
     * @param bool|null         $alwaysUseAuthenticatedGroups If enabled (`true`), the authenticated groups will always be used even when an authenticated consumer already… Default: `false`.
     * @param list<string>|null $deny                         Arbitrary group names that are not allowed to consume the service or route.
     * @param list<string>|null $denyWhen                     Deny the request if it matches any of these CEL boolean expressions evaluated against the request context (co…
     * @param bool|null         $hideGroupsHeader             If enabled (`true`), prevents the `X-Consumer-Groups` header from being sent in the request to the upstream s… Default: `false`.
     * @param bool|null         $includeConsumerGroups        If enabled (`true`), allows the consumer-groups to be used in the `allow|deny` fields. Default: `false`.
     */
    public function __construct(
        public ?array $allow = null,
        public ?array $allowWhen = null,
        public ?bool $alwaysUseAuthenticatedGroups = null,
        public ?array $deny = null,
        public ?array $denyWhen = null,
        public ?bool $hideGroupsHeader = null,
        public ?bool $includeConsumerGroups = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'allow' => $this->allow,
            'allow_when' => $this->allowWhen,
            'always_use_authenticated_groups' => $this->alwaysUseAuthenticatedGroups,
            'deny' => $this->deny,
            'deny_when' => $this->denyWhen,
            'hide_groups_header' => $this->hideGroupsHeader,
            'include_consumer_groups' => $this->includeConsumerGroups,
        ]);
    }
}
