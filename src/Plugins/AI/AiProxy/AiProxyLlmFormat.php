<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiProxy;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.llm_format` in the AI Proxy Plugin doc.
 *
 * LLM input and output format and schema to use
 */
#[PluginSchema('AI/ai-proxy.md', '#/properties/config/properties/llm_format')]
enum AiProxyLlmFormat: string
{
    case Anthropic = 'anthropic';
    case Bedrock = 'bedrock';
    case Cohere = 'cohere';
    case Gemini = 'gemini';
    case Huggingface = 'huggingface';
    case Openai = 'openai';
}
