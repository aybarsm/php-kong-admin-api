<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Security\IpRestriction;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `ip-restriction` plugin (IP Restriction; doc `Security/ip-restriction.md`).
 *
 * Read it from a returned Plugin with `IpRestrictionConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('Security/ip-restriction.md', '#/properties/config')]
final readonly class IpRestrictionConfig implements PluginConfig
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'ip-restriction';

    /**
     * @param list<string>|null $allow   List of IPs or CIDR ranges to allow.
     * @param list<string>|null $deny    List of IPs or CIDR ranges to deny.
     * @param string|null       $message The message to send as a response body to rejected requests.
     * @param int|float|null    $status  The HTTP status of the requests that will be rejected by the plugin.
     */
    public function __construct(
        public ?array $allow = null,
        public ?array $deny = null,
        public ?string $message = null,
        public int|float|null $status = null,
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
            deny: Data::stringListOrNull($data, 'deny'),
            message: Data::stringOrNull($data, 'message'),
            status: Data::numberOrNull($data, 'status'),
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
            'deny' => $this->deny,
            'message' => $this->message,
            'status' => $this->status,
        ]);
    }

    /**
     * Reads the typed configuration of a `ip-restriction` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `ip-restriction` plugin
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
