<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiPromptGuard;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `ai-prompt-guard` plugin (AI Prompt Guard; doc `AI/ai-prompt-guard.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('AI/ai-prompt-guard.md', '#/properties/config')]
final readonly class AiPromptGuardConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'ai-prompt-guard';

    /**
     * @param bool|null          $allowAllConversationHistory If true, will ignore all previous chat prompts from the conversation history. Default: `false`.
     * @param list<string>|null  $allowPatterns               Array of valid regex patterns, or valid questions from the 'user' role in chat.
     * @param list<string>|null  $denyPatterns                Array of invalid regex patterns, or invalid questions from the 'user' role in chat.
     * @param GenaiCategory|null $genaiCategory               Generative AI category of the request Default: `text/generation`.
     * @param LlmFormat|null     $llmFormat                   LLM input and output format and schema to use Default: `openai`.
     * @param bool|null          $matchAllRoles               If true, will match all roles in addition to 'user' role in conversation history. Default: `false`.
     * @param int|null           $maxRequestBodySize          max allowed body size allowed to be introspected. Default: `1048576`.
     */
    public function __construct(
        public ?bool $allowAllConversationHistory = null,
        public ?array $allowPatterns = null,
        public ?array $denyPatterns = null,
        public ?GenaiCategory $genaiCategory = null,
        public ?LlmFormat $llmFormat = null,
        public ?bool $matchAllRoles = null,
        public ?int $maxRequestBodySize = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'allow_all_conversation_history' => $this->allowAllConversationHistory,
            'allow_patterns' => $this->allowPatterns,
            'deny_patterns' => $this->denyPatterns,
            'genai_category' => $this->genaiCategory?->value,
            'llm_format' => $this->llmFormat?->value,
            'match_all_roles' => $this->matchAllRoles,
            'max_request_body_size' => $this->maxRequestBodySize,
        ]);
    }
}
