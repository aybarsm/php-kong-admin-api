<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiResponseTransformer;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.llm` object of the AI Response Transformer plugin (doc `AI/ai-response-transformer.md`).
 */
#[PluginSchema('AI/ai-response-transformer.md', '#/properties/config/properties/llm')]
final readonly class AiResponseTransformerLlm implements Model
{
    /**
     * @param AiResponseTransformerLlmModel        $model
     * @param AiResponseTransformerLlmRouteType    $routeType   The model's operation implementation, for this provider.
     * @param AiResponseTransformerLlmAuth|null    $auth
     * @param string|null                          $description The semantic description of the target, required if using semantic load balancing.
     * @param AiResponseTransformerLlmLogging|null $logging
     * @param array<array-key, mixed>|null         $metadata    For internal use only.
     * @param int|null                             $weight      The weight this target gets within the upstream loadbalancer (1-65535). Default: `100`.
     */
    public function __construct(
        public AiResponseTransformerLlmModel $model,
        public AiResponseTransformerLlmRouteType $routeType,
        public ?AiResponseTransformerLlmAuth $auth = null,
        public ?string $description = null,
        public ?AiResponseTransformerLlmLogging $logging = null,
        public ?array $metadata = null,
        public ?int $weight = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $auth = Data::mapOrNull($data, 'auth');
        $logging = Data::mapOrNull($data, 'logging');

        return new self(
            model: AiResponseTransformerLlmModel::fromArray(Data::map($data, 'model')),
            routeType: Data::enum($data, 'route_type', AiResponseTransformerLlmRouteType::class),
            auth: $auth === null ? null : AiResponseTransformerLlmAuth::fromArray($auth),
            description: Data::stringOrNull($data, 'description'),
            logging: $logging === null ? null : AiResponseTransformerLlmLogging::fromArray($logging),
            metadata: Data::freeFormOrNull($data, 'metadata'),
            weight: Data::intOrNull($data, 'weight'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'model' => $this->model->toArray(),
            'route_type' => $this->routeType->value,
            'auth' => $this->auth?->toArray(),
            'description' => $this->description,
            'logging' => $this->logging?->toArray(),
            'metadata' => $this->metadata,
            'weight' => $this->weight,
        ]);
    }
}
