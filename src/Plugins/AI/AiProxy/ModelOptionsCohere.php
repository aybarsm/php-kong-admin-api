<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiProxy;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.model.options.cohere` object of the AI Proxy plugin (doc `AI/ai-proxy.md`).
 */
#[PluginSchema('AI/ai-proxy.md', '#/properties/config/properties/model/properties/options/properties/cohere')]
final readonly class ModelOptionsCohere implements Model
{
    /**
     * @param ModelOptionsCohereEmbeddingInputType|null $embeddingInputType The purpose of the input text to calculate embedding vectors. Default: `classification`.
     * @param bool|null                                 $waitForModel       Wait for the model if it is not ready
     */
    public function __construct(
        public ?ModelOptionsCohereEmbeddingInputType $embeddingInputType = null,
        public ?bool $waitForModel = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            embeddingInputType: Data::enumOrNull($data, 'embedding_input_type', ModelOptionsCohereEmbeddingInputType::class),
            waitForModel: Data::boolOrNull($data, 'wait_for_model'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'embedding_input_type' => $this->embeddingInputType?->value,
            'wait_for_model' => $this->waitForModel,
        ]);
    }
}
