<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiProxy;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.model` object of the AI Proxy plugin (doc `AI/ai-proxy.md`).
 */
#[PluginSchema('AI/ai-proxy.md', '#/properties/config/properties/model')]
final readonly class AiProxyModel implements Model
{
    /**
     * @param AiProxyModelProvider     $provider   AI provider request format - Kong translates requests to and from the specified backend compatible formats.
     * @param string|null              $modelAlias The model name parameter from the request that this model should map to.
     * @param string|null              $name       Model name to execute.
     * @param AiProxyModelOptions|null $options    Key/value settings for the model
     */
    public function __construct(
        public AiProxyModelProvider $provider,
        public ?string $modelAlias = null,
        public ?string $name = null,
        public ?AiProxyModelOptions $options = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $options = Data::mapOrNull($data, 'options');

        return new self(
            provider: Data::enum($data, 'provider', AiProxyModelProvider::class),
            modelAlias: Data::stringOrNull($data, 'model_alias'),
            name: Data::stringOrNull($data, 'name'),
            options: $options === null ? null : AiProxyModelOptions::fromArray($options),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'provider' => $this->provider->value,
            'model_alias' => $this->modelAlias,
            'name' => $this->name,
            'options' => $this->options?->toArray(),
        ]);
    }
}
