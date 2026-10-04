<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiRequestTransformer;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.llm` object of the AI Request Transformer plugin (doc `AI/ai-request-transformer.md`).
 */
#[PluginSchema('AI/ai-request-transformer.md', '#/properties/config/properties/llm')]
final readonly class Llm implements Model
{
    /**
     * @param LlmModel                     $model
     * @param LlmRouteType                 $routeType   The model's operation implementation, for this provider.
     * @param LlmAuth|null                 $auth
     * @param string|null                  $description The semantic description of the target, required if using semantic load balancing.
     * @param LlmLogging|null              $logging
     * @param array<array-key, mixed>|null $metadata    For internal use only.
     * @param int|null                     $weight      The weight this target gets within the upstream loadbalancer (1-65535). Default: `100`.
     */
    public function __construct(
        public LlmModel $model,
        public LlmRouteType $routeType,
        public ?LlmAuth $auth = null,
        public ?string $description = null,
        public ?LlmLogging $logging = null,
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
            model: LlmModel::fromArray(Data::map($data, 'model')),
            routeType: Data::enum($data, 'route_type', LlmRouteType::class),
            auth: $auth === null ? null : LlmAuth::fromArray($auth),
            description: Data::stringOrNull($data, 'description'),
            logging: $logging === null ? null : LlmLogging::fromArray($logging),
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
