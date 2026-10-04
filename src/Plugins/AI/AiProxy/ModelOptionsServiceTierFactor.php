<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiProxy;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.model.options.service_tier_factor[]` object of the AI Proxy plugin (doc `AI/ai-proxy.md`).
 */
#[PluginSchema('AI/ai-proxy.md', '#/properties/config/properties/model/properties/options/properties/service_tier_factor/items')]
final readonly class ModelOptionsServiceTierFactor implements Model
{
    /**
     * @param int|float $factor
     * @param string    $tier   A word matched case-insensitively as a substring of the vendor's reported service tier (e.g.
     */
    public function __construct(
        public int|float $factor,
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
            factor: Data::number($data, 'factor'),
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
