<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\AccessControlEnforcement;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `ace` plugin (Access Control Enforcement; doc `TrafficControl/access-control-enforcement.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('TrafficControl/access-control-enforcement.md', '#/properties/config')]
final readonly class AccessControlEnforcementConfigInput implements Input
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
}
