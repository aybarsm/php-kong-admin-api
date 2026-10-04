<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\AccessControlEnforcement;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.rate_limiting.redis.cloud_authentication.oauth.grant_type` in the Access Control Enforcement Plugin doc.
 *
 * OAuth 2.0 grant type used to request access tokens.
 */
#[PluginSchema('TrafficControl/access-control-enforcement.md', '#/properties/config/properties/rate_limiting/properties/redis/properties/cloud_authentication/properties/oauth/properties/grant_type')]
enum RateLimitingRedisCloudAuthenticationOauthGrantType: string
{
    case ClientCredentials = 'client_credentials';
    case Password = 'password';
}
