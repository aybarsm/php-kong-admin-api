<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiProxy;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.model.options.huggingface` object of the AI Proxy plugin (doc `AI/ai-proxy.md`).
 */
#[PluginSchema('AI/ai-proxy.md', '#/properties/config/properties/model/properties/options/properties/huggingface')]
final readonly class AiProxyModelOptionsHuggingface implements Model
{
    /**
     * @param bool|null $useCache     Use the cache layer on the inference API
     * @param bool|null $waitForModel Wait for the model if it is not ready
     */
    public function __construct(
        public ?bool $useCache = null,
        public ?bool $waitForModel = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            useCache: Data::boolOrNull($data, 'use_cache'),
            waitForModel: Data::boolOrNull($data, 'wait_for_model'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'use_cache' => $this->useCache,
            'wait_for_model' => $this->waitForModel,
        ]);
    }
}
