<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\BasicAuth;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.brute_force_protection.redis.cloud_authentication.oauth.auth_method` in the Basic Auth Plugin doc.
 *
 * Client authentication method used against the token endpoint.
 */
#[PluginSchema('Authentication/basic-auth.md', '#/properties/config/properties/brute_force_protection/properties/redis/properties/cloud_authentication/properties/oauth/properties/auth_method')]
enum BasicAuthBruteForceProtectionRedisCloudAuthenticationOauthAuthMethod: string
{
    case ClientSecretBasic = 'client_secret_basic';
    case ClientSecretJwt = 'client_secret_jwt';
    case ClientSecretPost = 'client_secret_post';
}
