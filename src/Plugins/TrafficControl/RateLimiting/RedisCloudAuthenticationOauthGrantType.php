<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\RateLimiting;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.redis.cloud_authentication.oauth.grant_type` in the Rate Limiting Plugin doc.
 *
 * OAuth 2.0 grant type used to request access tokens.
 */
#[PluginSchema('TrafficControl/rate-limiting.md', '#/properties/config/properties/redis/properties/cloud_authentication/properties/oauth/properties/grant_type')]
enum RedisCloudAuthenticationOauthGrantType: string
{
    case ClientCredentials = 'client_credentials';
    case Password = 'password';
}
