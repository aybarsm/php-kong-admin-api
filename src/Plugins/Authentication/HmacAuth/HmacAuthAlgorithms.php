<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\HmacAuth;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.algorithms[]` in the HMAC Auth Plugin doc.
 */
#[PluginSchema('Authentication/hmac-auth.md', '#/properties/config/properties/algorithms/items')]
enum HmacAuthAlgorithms: string
{
    case HmacSha1 = 'hmac-sha1';
    case HmacSha224 = 'hmac-sha224';
    case HmacSha256 = 'hmac-sha256';
    case HmacSha384 = 'hmac-sha384';
    case HmacSha512 = 'hmac-sha512';
}
