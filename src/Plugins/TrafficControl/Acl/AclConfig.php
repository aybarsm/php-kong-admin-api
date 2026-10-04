<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\Acl;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `acl` plugin (ACL; doc `TrafficControl/acl.md`).
 *
 * Read it from a returned Plugin with `AclConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('TrafficControl/acl.md', '#/properties/config')]
final readonly class AclConfig implements PluginConfig
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
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            allow: Data::stringListOrNull($data, 'allow'),
            allowWhen: Data::stringListOrNull($data, 'allow_when'),
            alwaysUseAuthenticatedGroups: Data::boolOrNull($data, 'always_use_authenticated_groups'),
            deny: Data::stringListOrNull($data, 'deny'),
            denyWhen: Data::stringListOrNull($data, 'deny_when'),
            hideGroupsHeader: Data::boolOrNull($data, 'hide_groups_header'),
            includeConsumerGroups: Data::boolOrNull($data, 'include_consumer_groups'),
        );
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

    /**
     * Reads the typed configuration of a `acl` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `acl` plugin
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
