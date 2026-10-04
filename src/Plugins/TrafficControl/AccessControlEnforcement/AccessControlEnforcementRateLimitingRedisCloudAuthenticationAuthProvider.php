<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\AccessControlEnforcement;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.rate_limiting.redis.cloud_authentication.auth_provider` in the Access Control Enforcement Plugin doc.
 *
 * Auth providers to be used to authenticate to a Cloud Provider's Redis instance.
 */
#[PluginSchema('TrafficControl/access-control-enforcement.md', '#/properties/config/properties/rate_limiting/properties/redis/properties/cloud_authentication/properties/auth_provider')]
enum AccessControlEnforcementRateLimitingRedisCloudAuthenticationAuthProvider: string
{
    case Aws = 'aws';
    case Azure = 'azure';
    case Gcp = 'gcp';
    case Oauth = 'oauth';
}
