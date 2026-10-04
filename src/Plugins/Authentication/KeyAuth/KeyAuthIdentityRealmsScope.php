<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\KeyAuth;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.identity_realms[].scope` in the Key Auth Plugin doc.
 */
#[PluginSchema('Authentication/key-auth.md', '#/properties/config/properties/identity_realms/items/properties/scope')]
enum KeyAuthIdentityRealmsScope: string
{
    case Cp = 'cp';
    case Realm = 'realm';
}
