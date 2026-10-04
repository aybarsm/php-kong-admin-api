<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiRequestTransformer;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.llm.logging` object of the AI Request Transformer plugin (doc `AI/ai-request-transformer.md`).
 */
#[PluginSchema('AI/ai-request-transformer.md', '#/properties/config/properties/llm/properties/logging')]
final readonly class AiRequestTransformerLlmLogging implements Model
{
    /**
     * @param bool|null $logPayloads   If enabled, will log the request and response body into the Kong log plugin(s) output.Furthermore if Opentele… Default: `false`.
     * @param bool|null $logStatistics If enabled and supported by the driver, will add model usage and token metrics into the Kong log plugin(s) ou… Default: `false`.
     */
    public function __construct(
        public ?bool $logPayloads = null,
        public ?bool $logStatistics = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            logPayloads: Data::boolOrNull($data, 'log_payloads'),
            logStatistics: Data::boolOrNull($data, 'log_statistics'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'log_payloads' => $this->logPayloads,
            'log_statistics' => $this->logStatistics,
        ]);
    }
}
