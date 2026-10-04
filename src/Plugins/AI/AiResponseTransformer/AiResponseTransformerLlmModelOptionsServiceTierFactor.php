<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiResponseTransformer;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.llm.model.options.service_tier_factor[]` object of the AI Response Transformer plugin (doc `AI/ai-response-transformer.md`).
 */
#[PluginSchema('AI/ai-response-transformer.md', '#/properties/config/properties/llm/properties/model/properties/options/properties/service_tier_factor/items')]
final readonly class AiResponseTransformerLlmModelOptionsServiceTierFactor implements Model
{
    /**
     * @param float  $factor
     * @param string $tier   A word matched case-insensitively as a substring of the vendor's reported service tier (e.g.
     */
    public function __construct(
        public float $factor,
        public string $tier,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            factor: Data::float($data, 'factor'),
            tier: Data::string($data, 'tier'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'factor' => $this->factor,
            'tier' => $this->tier,
        ]);
    }
}
