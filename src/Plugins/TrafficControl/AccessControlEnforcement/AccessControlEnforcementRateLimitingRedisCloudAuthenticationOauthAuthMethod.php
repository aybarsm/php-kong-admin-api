<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\AccessControlEnforcement;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.rate_limiting.redis.cloud_authentication.oauth.auth_method` in the Access Control Enforcement Plugin doc.
 *
 * Client authentication method used against the token endpoint.
 */
#[PluginSchema('TrafficControl/access-control-enforcement.md', '#/properties/config/properties/rate_limiting/properties/redis/properties/cloud_authentication/properties/oauth/properties/auth_method')]
enum AccessControlEnforcementRateLimitingRedisCloudAuthenticationOauthAuthMethod: string
{
    case ClientSecretBasic = 'client_secret_basic';
    case ClientSecretJwt = 'client_secret_jwt';
    case ClientSecretPost = 'client_secret_post';
}
