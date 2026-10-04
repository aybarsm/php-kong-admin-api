<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\Jwt;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.claims_to_verify[]` in the JWT Plugin doc.
 */
#[PluginSchema('Authentication/jwt.md', '#/properties/config/properties/claims_to_verify/items')]
enum JwtClaimsToVerify: string
{
    case Exp = 'exp';
    case Nbf = 'nbf';
}
