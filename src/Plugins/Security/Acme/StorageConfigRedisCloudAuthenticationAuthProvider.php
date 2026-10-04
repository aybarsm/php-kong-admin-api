<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Security\Acme;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.storage_config.redis.cloud_authentication.auth_provider` in the ACME Plugin doc.
 *
 * Auth providers to be used to authenticate to a Cloud Provider's Redis instance.
 */
#[PluginSchema('Security/acme.md', '#/properties/config/properties/storage_config/properties/redis/properties/cloud_authentication/properties/auth_provider')]
enum StorageConfigRedisCloudAuthenticationAuthProvider: string
{
    case Aws = 'aws';
    case Azure = 'azure';
    case Gcp = 'gcp';
    case Oauth = 'oauth';
}
