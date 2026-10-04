<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiRequestTransformer;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.llm.model.options.mistral_format` in the AI Request Transformer Plugin doc.
 *
 * If using mistral provider, select the upstream message format.
 */
#[PluginSchema('AI/ai-request-transformer.md', '#/properties/config/properties/llm/properties/model/properties/options/properties/mistral_format')]
enum AiRequestTransformerLlmModelOptionsMistralFormat: string
{
    case Ollama = 'ollama';
    case Openai = 'openai';
}
