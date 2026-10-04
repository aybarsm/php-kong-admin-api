<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\BasicAuth;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.brute_force_protection.strategy` in the Basic Auth Plugin doc.
 *
 * The brute force protection strategy to use for retrieving and incrementing the limits.
 */
#[PluginSchema('Authentication/basic-auth.md', '#/properties/config/properties/brute_force_protection/properties/strategy')]
enum BruteForceProtectionStrategy: string
{
    case Cluster = 'cluster';
    case Memory = 'memory';
    case Off = 'off';
    case Redis = 'redis';
}
