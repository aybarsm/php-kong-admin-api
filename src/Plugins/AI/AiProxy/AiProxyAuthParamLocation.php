<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiProxy;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.auth.param_location` in the AI Proxy Plugin doc.
 *
 * Specify whether the 'param_name' and 'param_value' options go in a query string, or the POST form/JSON body.
 */
#[PluginSchema('AI/ai-proxy.md', '#/properties/config/properties/auth/properties/param_location')]
enum AiProxyAuthParamLocation: string
{
    case Body = 'body';
    case Query = 'query';
}
