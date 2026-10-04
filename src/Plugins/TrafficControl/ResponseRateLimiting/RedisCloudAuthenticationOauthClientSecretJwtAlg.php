<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\ResponseRateLimiting;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.redis.cloud_authentication.oauth.client_secret_jwt_alg` in the Response Rate Limiting Plugin doc.
 *
 * Signing algorithm used for `client_secret_jwt` client authentication.
 */
#[PluginSchema('TrafficControl/response-rate-limiting.md', '#/properties/config/properties/redis/properties/cloud_authentication/properties/oauth/properties/client_secret_jwt_alg')]
enum RedisCloudAuthenticationOauthClientSecretJwtAlg: string
{
    case Hs256 = 'HS256';
    case Hs512 = 'HS512';
}
