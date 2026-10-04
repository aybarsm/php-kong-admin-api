<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\BasicAuth;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.brute_force_protection.redis.cloud_authentication.oauth.grant_type` in the Basic Auth Plugin doc.
 *
 * OAuth 2.0 grant type used to request access tokens.
 */
#[PluginSchema('Authentication/basic-auth.md', '#/properties/config/properties/brute_force_protection/properties/redis/properties/cloud_authentication/properties/oauth/properties/grant_type')]
enum BruteForceProtectionRedisCloudAuthenticationOauthGrantType: string
{
    case ClientCredentials = 'client_credentials';
    case Password = 'password';
}
