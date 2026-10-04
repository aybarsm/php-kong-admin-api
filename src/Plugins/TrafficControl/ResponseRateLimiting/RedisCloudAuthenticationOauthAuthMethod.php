<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\ResponseRateLimiting;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.redis.cloud_authentication.oauth.auth_method` in the Response Rate Limiting Plugin doc.
 *
 * Client authentication method used against the token endpoint.
 */
#[PluginSchema('TrafficControl/response-rate-limiting.md', '#/properties/config/properties/redis/properties/cloud_authentication/properties/oauth/properties/auth_method')]
enum RedisCloudAuthenticationOauthAuthMethod: string
{
    case ClientSecretBasic = 'client_secret_basic';
    case ClientSecretJwt = 'client_secret_jwt';
    case ClientSecretPost = 'client_secret_post';
}
