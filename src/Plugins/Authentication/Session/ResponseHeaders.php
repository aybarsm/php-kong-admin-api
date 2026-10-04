<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\Session;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.response_headers[]` in the Session Plugin doc.
 */
#[PluginSchema('Authentication/session.md', '#/properties/config/properties/response_headers/items')]
enum ResponseHeaders: string
{
    case AbsoluteTimeout = 'absolute-timeout';
    case Audience = 'audience';
    case Id = 'id';
    case IdlingTimeout = 'idling-timeout';
    case RollingTimeout = 'rolling-timeout';
    case Subject = 'subject';
    case Timeout = 'timeout';
}
