<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiProxy;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.model.options.cache_write_cost_list[]` object of the AI Proxy plugin (doc `AI/ai-proxy.md`).
 */
#[PluginSchema('AI/ai-proxy.md', '#/properties/config/properties/model/properties/options/properties/cache_write_cost_list/items')]
final readonly class ModelOptionsCacheWriteCostList implements Model
{
    /**
     * @param int|float $cost
     * @param string    $ttl
     */
    public function __construct(
        public int|float $cost,
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
            cost: Data::number($data, 'cost'),
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
