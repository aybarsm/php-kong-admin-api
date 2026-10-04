<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiResponseTransformer;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.llm.model.provider` in the AI Response Transformer Plugin doc.
 *
 * AI provider request format - Kong translates requests to and from the specified backend compatible formats.
 */
#[PluginSchema('AI/ai-response-transformer.md', '#/properties/config/properties/llm/properties/model/properties/provider')]
enum AiResponseTransformerLlmModelProvider: string
{
    case Anthropic = 'anthropic';
    case Azure = 'azure';
    case Bedrock = 'bedrock';
    case Cerebras = 'cerebras';
    case Cohere = 'cohere';
    case Dashscope = 'dashscope';
    case Databricks = 'databricks';
    case Deepseek = 'deepseek';
    case Gemini = 'gemini';
    case Huggingface = 'huggingface';
    case Llama2 = 'llama2';
    case Mistral = 'mistral';
    case Ollama = 'ollama';
    case Openai = 'openai';
    case Vllm = 'vllm';
    case Xai = 'xai';
}
