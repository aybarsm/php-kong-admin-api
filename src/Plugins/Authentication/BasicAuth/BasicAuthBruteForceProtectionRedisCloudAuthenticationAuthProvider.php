<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\BasicAuth;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.brute_force_protection.redis.cloud_authentication.auth_provider` in the Basic Auth Plugin doc.
 *
 * Auth providers to be used to authenticate to a Cloud Provider's Redis instance.
 */
#[PluginSchema('Authentication/basic-auth.md', '#/properties/config/properties/brute_force_protection/properties/redis/properties/cloud_authentication/properties/auth_provider')]
enum BasicAuthBruteForceProtectionRedisCloudAuthenticationAuthProvider: string
{
    case Aws = 'aws';
    case Azure = 'azure';
    case Gcp = 'gcp';
    case Oauth = 'oauth';
}
