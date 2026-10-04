<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Security\Acme;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.storage_config.redis.cloud_authentication.oauth.client_secret_jwt_alg` in the ACME Plugin doc.
 *
 * Signing algorithm used for `client_secret_jwt` client authentication.
 */
#[PluginSchema('Security/acme.md', '#/properties/config/properties/storage_config/properties/redis/properties/cloud_authentication/properties/oauth/properties/client_secret_jwt_alg')]
enum StorageConfigRedisCloudAuthenticationOauthClientSecretJwtAlg: string
{
    case Hs256 = 'HS256';
    case Hs512 = 'HS512';
}
