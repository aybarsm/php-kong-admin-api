<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiPromptDecorator;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.prompts` object of the AI Prompt Decorator plugin (doc `AI/ai-prompt-decorator.md`).
 */
#[PluginSchema('AI/ai-prompt-decorator.md', '#/properties/config/properties/prompts')]
final readonly class Prompts implements Model
{
    /**
     * @param list<PromptsAppend>|null  $append  Insert chat messages at the end of the chat message array.
     * @param list<PromptsPrepend>|null $prepend Insert chat messages at the beginning of the chat message array.
     */
    public function __construct(
        public ?array $append = null,
        public ?array $prepend = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $append = Data::listOfMapsOrNull($data, 'append');
        $prepend = Data::listOfMapsOrNull($data, 'prepend');

        return new self(
            append: $append === null ? null : array_map(PromptsAppend::fromArray(...), $append),
            prepend: $prepend === null ? null : array_map(PromptsPrepend::fromArray(...), $prepend),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'append' => Data::toArrays($this->append),
            'prepend' => Data::toArrays($this->prepend),
        ]);
    }
}
