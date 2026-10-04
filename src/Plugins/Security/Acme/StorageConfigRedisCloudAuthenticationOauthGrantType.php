<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Security\Acme;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.storage_config.redis.cloud_authentication.oauth.grant_type` in the ACME Plugin doc.
 *
 * OAuth 2.0 grant type used to request access tokens.
 */
#[PluginSchema('Security/acme.md', '#/properties/config/properties/storage_config/properties/redis/properties/cloud_authentication/properties/oauth/properties/grant_type')]
enum StorageConfigRedisCloudAuthenticationOauthGrantType: string
{
    case ClientCredentials = 'client_credentials';
    case Password = 'password';
}
