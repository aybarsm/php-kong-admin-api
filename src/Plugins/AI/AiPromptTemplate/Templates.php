<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiPromptTemplate;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.templates[]` object of the AI Prompt Template plugin (doc `AI/ai-prompt-template.md`).
 */
#[PluginSchema('AI/ai-prompt-template.md', '#/properties/config/properties/templates/items')]
final readonly class Templates implements Model
{
    /**
     * @param string $name     Unique name for the template, can be called with `{template://NAME}`
     * @param string $template Template string for this request, supports mustache-style `{{placeholders}}`
     */
    public function __construct(
        public string $name,
        public string $template,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            name: Data::string($data, 'name'),
            template: Data::string($data, 'template'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'name' => $this->name,
            'template' => $this->template,
        ]);
    }
}
