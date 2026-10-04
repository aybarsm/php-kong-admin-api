<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiRequestTransformer;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.llm.auth.param_location` in the AI Request Transformer Plugin doc.
 *
 * Specify whether the 'param_name' and 'param_value' options go in a query string, or the POST form/JSON body.
 */
#[PluginSchema('AI/ai-request-transformer.md', '#/properties/config/properties/llm/properties/auth/properties/param_location')]
enum LlmAuthParamLocation: string
{
    case Body = 'body';
    case Query = 'query';
}
