<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiPromptDecorator;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.prompts.append[].role` in the AI Prompt Decorator Plugin doc.
 */
#[PluginSchema('AI/ai-prompt-decorator.md', '#/properties/config/properties/prompts/properties/append/items/properties/role')]
enum PromptsAppendRole: string
{
    case Assistant = 'assistant';
    case System = 'system';
    case User = 'user';
}
