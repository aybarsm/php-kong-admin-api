<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\AccessControlEnforcement;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.rate_limiting.redis.cloud_authentication.oauth.client_secret_jwt_alg` in the Access Control Enforcement Plugin doc.
 *
 * Signing algorithm used for `client_secret_jwt` client authentication.
 */
#[PluginSchema('TrafficControl/access-control-enforcement.md', '#/properties/config/properties/rate_limiting/properties/redis/properties/cloud_authentication/properties/oauth/properties/client_secret_jwt_alg')]
enum AccessControlEnforcementRateLimitingRedisCloudAuthenticationOauthClientSecretJwtAlg: string
{
    case Hs256 = 'HS256';
    case Hs512 = 'HS512';
}
