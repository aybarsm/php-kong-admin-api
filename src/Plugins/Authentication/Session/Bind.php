<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\Session;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.bind[]` in the Session Plugin doc.
 */
#[PluginSchema('Authentication/session.md', '#/properties/config/properties/bind/items')]
enum Bind: string
{
    case Ip = 'ip';
    case Scheme = 'scheme';
    case UserAgent = 'user-agent';
}
