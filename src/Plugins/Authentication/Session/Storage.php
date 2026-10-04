<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\Session;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.storage` in the Session Plugin doc.
 *
 * Determines where the session data is stored.
 */
#[PluginSchema('Authentication/session.md', '#/properties/config/properties/storage')]
enum Storage: string
{
    case Cookie = 'cookie';
    case Kong = 'kong';
}
