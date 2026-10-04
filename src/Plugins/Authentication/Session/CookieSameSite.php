<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\Session;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.cookie_same_site` in the Session Plugin doc.
 *
 * Determines whether and how a cookie may be sent with cross-site requests.
 */
#[PluginSchema('Authentication/session.md', '#/properties/config/properties/cookie_same_site')]
enum CookieSameSite: string
{
    case Default = 'Default';
    case Lax = 'Lax';
    case None = 'None';
    case Strict = 'Strict';
}
