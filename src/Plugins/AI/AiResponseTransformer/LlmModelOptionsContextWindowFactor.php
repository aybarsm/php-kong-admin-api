<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiResponseTransformer;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.llm.model.options.context_window_factor[]` object of the AI Response Transformer plugin (doc `AI/ai-response-transformer.md`).
 */
#[PluginSchema('AI/ai-response-transformer.md', '#/properties/config/properties/llm/properties/model/properties/options/properties/context_window_factor/items')]
final readonly class LlmModelOptionsContextWindowFactor implements Model
{
    /**
     * @param string    $above
     * @param int|float $inputFactor
     * @param int|float $outputFactor
     */
    public function __construct(
        public string $above,
        public int|float $inputFactor,
        public int|float $outputFactor,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            above: Data::string($data, 'above'),
            inputFactor: Data::number($data, 'input_factor'),
            outputFactor: Data::number($data, 'output_factor'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'above' => $this->above,
            'input_factor' => $this->inputFactor,
            'output_factor' => $this->outputFactor,
        ]);
    }
}
