<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Security\Acme;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.storage_config.redis.cloud_authentication.oauth.auth_method` in the ACME Plugin doc.
 *
 * Client authentication method used against the token endpoint.
 */
#[PluginSchema('Security/acme.md', '#/properties/config/properties/storage_config/properties/redis/properties/cloud_authentication/properties/oauth/properties/auth_method')]
enum AcmeStorageConfigRedisCloudAuthenticationOauthAuthMethod: string
{
    case ClientSecretBasic = 'client_secret_basic';
    case ClientSecretJwt = 'client_secret_jwt';
    case ClientSecretPost = 'client_secret_post';
}
