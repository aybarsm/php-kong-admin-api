<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\Oauth2;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.pkce` in the OAuth 2.0 Authentication Plugin doc.
 *
 * Specifies a mode of how the Proof Key for Code Exchange (PKCE) should be handled by the plugin.
 */
#[PluginSchema('Authentication/oauth2.md', '#/properties/config/properties/pkce')]
enum Pkce: string
{
    case Lax = 'lax';
    case None = 'none';
    case Strict = 'strict';
}
