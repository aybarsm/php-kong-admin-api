<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\AccessControlEnforcement;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `ace` plugin (Access Control Enforcement; doc `TrafficControl/access-control-enforcement.md`).
 *
 * Read it from a returned Plugin with `AccessControlEnforcementConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('TrafficControl/access-control-enforcement.md', '#/properties/config')]
final readonly class AccessControlEnforcementConfig implements PluginConfig
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'ace';

    /**
     * @param string|null       $anonymous    An optional string (consumer UUID or username) value to use as an `anonymous` consumer if authentication fail…
     * @param MatchPolicy|null  $matchPolicy  Determines how the ACE plugin will behave when a request doesn't match an existing operation from an API or A… Default: `if_present`.
     * @param RateLimiting|null $rateLimiting
     */
    public function __construct(
        public ?string $anonymous = null,
        public ?MatchPolicy $matchPolicy = null,
        public ?RateLimiting $rateLimiting = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $rateLimiting = Data::mapOrNull($data, 'rate_limiting');

        return new self(
            anonymous: Data::stringOrNull($data, 'anonymous'),
            matchPolicy: Data::enumOrNull($data, 'match_policy', MatchPolicy::class),
            rateLimiting: $rateLimiting === null ? null : RateLimiting::fromArray($rateLimiting),
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
            'match_policy' => $this->matchPolicy?->value,
            'rate_limiting' => $this->rateLimiting?->toArray(),
        ]);
    }

    /**
     * Reads the typed configuration of a `ace` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `ace` plugin
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
