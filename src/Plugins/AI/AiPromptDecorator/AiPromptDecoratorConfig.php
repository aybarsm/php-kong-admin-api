<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiPromptDecorator;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `ai-prompt-decorator` plugin (AI Prompt Decorator; doc `AI/ai-prompt-decorator.md`).
 *
 * Read it from a returned Plugin with `AiPromptDecoratorConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('AI/ai-prompt-decorator.md', '#/properties/config')]
final readonly class AiPromptDecoratorConfig implements PluginConfig
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'ai-prompt-decorator';

    /**
     * @param AiPromptDecoratorLlmFormat|null $llmFormat          LLM input and output format and schema to use Default: `openai`.
     * @param int|null                        $maxRequestBodySize max allowed body size allowed to be introspected. Default: `1048576`.
     * @param AiPromptDecoratorPrompts|null   $prompts
     */
    public function __construct(
        public ?AiPromptDecoratorLlmFormat $llmFormat = null,
        public ?int $maxRequestBodySize = null,
        public ?AiPromptDecoratorPrompts $prompts = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $prompts = Data::mapOrNull($data, 'prompts');

        return new self(
            llmFormat: Data::enumOrNull($data, 'llm_format', AiPromptDecoratorLlmFormat::class),
            maxRequestBodySize: Data::intOrNull($data, 'max_request_body_size'),
            prompts: $prompts === null ? null : AiPromptDecoratorPrompts::fromArray($prompts),
        );
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

    /**
     * Reads the typed configuration of a `ai-prompt-decorator` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `ai-prompt-decorator` plugin
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
