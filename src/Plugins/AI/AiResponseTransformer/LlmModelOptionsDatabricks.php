<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiResponseTransformer;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.llm.model.options.databricks` object of the AI Response Transformer plugin (doc `AI/ai-response-transformer.md`).
 */
#[PluginSchema('AI/ai-response-transformer.md', '#/properties/config/properties/llm/properties/model/properties/options/properties/databricks')]
final readonly class LlmModelOptionsDatabricks implements Model
{
    /**
     * @param string|null $workspaceInstanceId Workspace Instance ID ('dbc-xxx-yyy') for Databricks model serving.
     */
    public function __construct(
        public ?string $workspaceInstanceId = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            workspaceInstanceId: Data::stringOrNull($data, 'workspace_instance_id'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'workspace_instance_id' => $this->workspaceInstanceId,
        ]);
    }
}
