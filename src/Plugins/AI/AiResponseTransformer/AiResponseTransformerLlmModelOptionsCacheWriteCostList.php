<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiResponseTransformer;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.llm.model.options.cache_write_cost_list[]` object of the AI Response Transformer plugin (doc `AI/ai-response-transformer.md`).
 */
#[PluginSchema('AI/ai-response-transformer.md', '#/properties/config/properties/llm/properties/model/properties/options/properties/cache_write_cost_list/items')]
final readonly class AiResponseTransformerLlmModelOptionsCacheWriteCostList implements Model
{
    /**
     * @param float  $cost
     * @param string $ttl
     */
    public function __construct(
        public float $cost,
        public string $ttl,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            cost: Data::float($data, 'cost'),
            ttl: Data::string($data, 'ttl'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'cost' => $this->cost,
            'ttl' => $this->ttl,
        ]);
    }
}
