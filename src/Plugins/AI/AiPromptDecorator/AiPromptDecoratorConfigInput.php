<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiPromptDecorator;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `ai-prompt-decorator` plugin (AI Prompt Decorator; doc `AI/ai-prompt-decorator.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('AI/ai-prompt-decorator.md', '#/properties/config')]
final readonly class AiPromptDecoratorConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'ai-prompt-decorator';

    /**
     * @param LlmFormat|null $llmFormat          LLM input and output format and schema to use Default: `openai`.
     * @param int|null       $maxRequestBodySize max allowed body size allowed to be introspected. Default: `1048576`.
     * @param Prompts|null   $prompts
     */
    public function __construct(
        public ?LlmFormat $llmFormat = null,
        public ?int $maxRequestBodySize = null,
        public ?Prompts $prompts = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'llm_format' => $this->llmFormat?->value,
            'max_request_body_size' => $this->maxRequestBodySize,
            'prompts' => $this->prompts?->toArray(),
        ]);
    }
}
