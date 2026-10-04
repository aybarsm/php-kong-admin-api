<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiResponseTransformer;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.llm.model.options.dashscope` object of the AI Response Transformer plugin (doc `AI/ai-response-transformer.md`).
 */
#[PluginSchema('AI/ai-response-transformer.md', '#/properties/config/properties/llm/properties/model/properties/options/properties/dashscope')]
final readonly class LlmModelOptionsDashscope implements Model
{
    /**
     * @param bool|null $international Two Dashscope endpoints are available, and the international endpoint will be used when this is set to `true`. Default: `true`.
     */
    public function __construct(
        public ?bool $international = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            international: Data::boolOrNull($data, 'international'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'international' => $this->international,
        ]);
    }
}
