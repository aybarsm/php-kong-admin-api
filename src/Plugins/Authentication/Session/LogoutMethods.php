<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\Session;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.logout_methods[]` in the Session Plugin doc.
 */
#[PluginSchema('Authentication/session.md', '#/properties/config/properties/logout_methods/items')]
enum LogoutMethods: string
{
    case Delete = 'DELETE';
    case Get = 'GET';
    case Post = 'POST';
}
