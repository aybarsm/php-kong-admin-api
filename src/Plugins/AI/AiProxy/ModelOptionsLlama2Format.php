<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiProxy;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.model.options.llama2_format` in the AI Proxy Plugin doc.
 *
 * If using llama2 provider, select the upstream message format.
 */
#[PluginSchema('AI/ai-proxy.md', '#/properties/config/properties/model/properties/options/properties/llama2_format')]
enum ModelOptionsLlama2Format: string
{
    case Ollama = 'ollama';
    case Openai = 'openai';
    case Raw = 'raw';
}
