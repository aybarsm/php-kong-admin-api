<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiProxy;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.model.options.gemini` object of the AI Proxy plugin (doc `AI/ai-proxy.md`).
 */
#[PluginSchema('AI/ai-proxy.md', '#/properties/config/properties/model/properties/options/properties/gemini')]
final readonly class ModelOptionsGemini implements Model
{
    /**
     * @param string|null $apiEndpoint If running Gemini on Vertex, specify the regional API endpoint (hostname only).
     * @param string|null $endpointId  If running Gemini on Vertex Model Garden, specify the endpoint ID.
     * @param string|null $locationId  If running Gemini on Vertex, specify the location ID.
     * @param string|null $projectId   If running Gemini on Vertex, specify the project ID.
     */
    public function __construct(
        public ?string $apiEndpoint = null,
        public ?string $endpointId = null,
        public ?string $locationId = null,
        public ?string $projectId = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            apiEndpoint: Data::stringOrNull($data, 'api_endpoint'),
            endpointId: Data::stringOrNull($data, 'endpoint_id'),
            locationId: Data::stringOrNull($data, 'location_id'),
            projectId: Data::stringOrNull($data, 'project_id'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'api_endpoint' => $this->apiEndpoint,
            'endpoint_id' => $this->endpointId,
            'location_id' => $this->locationId,
            'project_id' => $this->projectId,
        ]);
    }
}
