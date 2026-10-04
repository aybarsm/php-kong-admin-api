<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiResponseTransformer;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.llm.model.options.mistral_format` in the AI Response Transformer Plugin doc.
 *
 * If using mistral provider, select the upstream message format.
 */
#[PluginSchema('AI/ai-response-transformer.md', '#/properties/config/properties/llm/properties/model/properties/options/properties/mistral_format')]
enum LlmModelOptionsMistralFormat: string
{
    case Ollama = 'ollama';
    case Openai = 'openai';
}
