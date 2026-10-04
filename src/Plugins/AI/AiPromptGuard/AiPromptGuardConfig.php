<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiPromptGuard;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `ai-prompt-guard` plugin (AI Prompt Guard; doc `AI/ai-prompt-guard.md`).
 *
 * Read it from a returned Plugin with `AiPromptGuardConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('AI/ai-prompt-guard.md', '#/properties/config')]
final readonly class AiPromptGuardConfig implements PluginConfig
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'ai-prompt-guard';

    /**
     * @param bool|null                       $allowAllConversationHistory If true, will ignore all previous chat prompts from the conversation history. Default: `false`.
     * @param list<string>|null               $allowPatterns               Array of valid regex patterns, or valid questions from the 'user' role in chat.
     * @param list<string>|null               $denyPatterns                Array of invalid regex patterns, or invalid questions from the 'user' role in chat.
     * @param AiPromptGuardGenaiCategory|null $genaiCategory               Generative AI category of the request Default: `text/generation`.
     * @param AiPromptGuardLlmFormat|null     $llmFormat                   LLM input and output format and schema to use Default: `openai`.
     * @param bool|null                       $matchAllRoles               If true, will match all roles in addition to 'user' role in conversation history. Default: `false`.
     * @param int|null                        $maxRequestBodySize          max allowed body size allowed to be introspected. Default: `1048576`.
     */
    public function __construct(
        public ?bool $allowAllConversationHistory = null,
        public ?array $allowPatterns = null,
        public ?array $denyPatterns = null,
        public ?AiPromptGuardGenaiCategory $genaiCategory = null,
        public ?AiPromptGuardLlmFormat $llmFormat = null,
        public ?bool $matchAllRoles = null,
        public ?int $maxRequestBodySize = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            allowAllConversationHistory: Data::boolOrNull($data, 'allow_all_conversation_history'),
            allowPatterns: Data::stringListOrNull($data, 'allow_patterns'),
            denyPatterns: Data::stringListOrNull($data, 'deny_patterns'),
            genaiCategory: Data::enumOrNull($data, 'genai_category', AiPromptGuardGenaiCategory::class),
            llmFormat: Data::enumOrNull($data, 'llm_format', AiPromptGuardLlmFormat::class),
            matchAllRoles: Data::boolOrNull($data, 'match_all_roles'),
            maxRequestBodySize: Data::intOrNull($data, 'max_request_body_size'),
        );
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

    /**
     * Reads the typed configuration of a `ai-prompt-guard` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `ai-prompt-guard` plugin
     * @throws UnexpectedResponseException when its `config` does not match the plugin doc
     */
    #[Override]
    public static function fromPlugin(Plugin $plugin): static
    {
        if ($plugin->name !== self::NAME) {
            throw new InvalidArgumentException(sprintf('Expected a "%s" plugin, got "%s".', self::NAME, $plugin->name));
        }

        return self::fromArray(Data::asMap($plugin->config ?? [], 'config'));
    }
}
