<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiProxy;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.model.options.databricks` object of the AI Proxy plugin (doc `AI/ai-proxy.md`).
 */
#[PluginSchema('AI/ai-proxy.md', '#/properties/config/properties/model/properties/options/properties/databricks')]
final readonly class AiProxyModelOptionsDatabricks implements Model
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
