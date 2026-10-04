<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\BasicAuth;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.brute_force_protection.redis.cloud_authentication.oauth.client_secret_jwt_alg` in the Basic Auth Plugin doc.
 *
 * Signing algorithm used for `client_secret_jwt` client authentication.
 */
#[PluginSchema('Authentication/basic-auth.md', '#/properties/config/properties/brute_force_protection/properties/redis/properties/cloud_authentication/properties/oauth/properties/client_secret_jwt_alg')]
enum BasicAuthBruteForceProtectionRedisCloudAuthenticationOauthClientSecretJwtAlg: string
{
    case Hs256 = 'HS256';
    case Hs512 = 'HS512';
}
