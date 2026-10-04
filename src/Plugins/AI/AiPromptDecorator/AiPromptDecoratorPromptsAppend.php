<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiPromptDecorator;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.prompts.append[]` object of the AI Prompt Decorator plugin (doc `AI/ai-prompt-decorator.md`).
 */
#[PluginSchema('AI/ai-prompt-decorator.md', '#/properties/config/properties/prompts/properties/append/items')]
final readonly class AiPromptDecoratorPromptsAppend implements Model
{
    /**
     * @param string                                  $content
     * @param AiPromptDecoratorPromptsAppendRole|null $role    Default: `system`.
     */
    public function __construct(
        public string $content,
        public ?AiPromptDecoratorPromptsAppendRole $role = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            content: Data::string($data, 'content'),
            role: Data::enumOrNull($data, 'role', AiPromptDecoratorPromptsAppendRole::class),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'content' => $this->content,
            'role' => $this->role?->value,
        ]);
    }
}
