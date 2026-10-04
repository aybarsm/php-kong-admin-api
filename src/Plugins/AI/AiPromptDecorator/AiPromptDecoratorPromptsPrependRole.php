<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiPromptDecorator;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.prompts.prepend[].role` in the AI Prompt Decorator Plugin doc.
 */
#[PluginSchema('AI/ai-prompt-decorator.md', '#/properties/config/properties/prompts/properties/prepend/items/properties/role')]
enum AiPromptDecoratorPromptsPrependRole: string
{
    case Assistant = 'assistant';
    case System = 'system';
    case User = 'user';
}
