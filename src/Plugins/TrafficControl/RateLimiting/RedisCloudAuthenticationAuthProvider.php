<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\RateLimiting;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.redis.cloud_authentication.auth_provider` in the Rate Limiting Plugin doc.
 *
 * Auth providers to be used to authenticate to a Cloud Provider's Redis instance.
 */
#[PluginSchema('TrafficControl/rate-limiting.md', '#/properties/config/properties/redis/properties/cloud_authentication/properties/auth_provider')]
enum RedisCloudAuthenticationAuthProvider: string
{
    case Aws = 'aws';
    case Azure = 'azure';
    case Gcp = 'gcp';
    case Oauth = 'oauth';
}
