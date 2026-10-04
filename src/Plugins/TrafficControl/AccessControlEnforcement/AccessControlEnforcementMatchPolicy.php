<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\AccessControlEnforcement;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.match_policy` in the Access Control Enforcement Plugin doc.
 *
 * Determines how the ACE plugin will behave when a request doesn't match an existing operation from an API or A…
 */
#[PluginSchema('TrafficControl/access-control-enforcement.md', '#/properties/config/properties/match_policy')]
enum AccessControlEnforcementMatchPolicy: string
{
    case IfPresent = 'if_present';
    case Required = 'required';
}
