<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiProxy;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.model.options.mistral_format` in the AI Proxy Plugin doc.
 *
 * If using mistral provider, select the upstream message format.
 */
#[PluginSchema('AI/ai-proxy.md', '#/properties/config/properties/model/properties/options/properties/mistral_format')]
enum ModelOptionsMistralFormat: string
{
    case Ollama = 'ollama';
    case Openai = 'openai';
}
