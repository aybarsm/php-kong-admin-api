<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiRequestTransformer;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.llm.model.options` object of the AI Request Transformer plugin (doc `AI/ai-request-transformer.md`).
 */
#[PluginSchema('AI/ai-request-transformer.md', '#/properties/config/properties/llm/properties/model/properties/options')]
final readonly class LlmModelOptions implements Model
{
    /**
     * @param string|null                                   $anthropicVersion     Defines the schema/API version, if using Anthropic provider.
     * @param string|null                                   $azureApiVersion      'api-version' for Azure OpenAI instances. Default: `2023-05-15`.
     * @param string|null                                   $azureDeploymentId    Deployment ID for Azure OpenAI instances.
     * @param string|null                                   $azureInstance        Instance name for Azure OpenAI hosted models.
     * @param LlmModelOptionsBedrock|null                   $bedrock
     * @param int|float|null                                $cacheReadCost        Defines the cost per 1M cache-read (cached) prompt tokens.
     * @param int|float|null                                $cacheWriteCost       Defines the cost per 1M cache-write prompt tokens.
     * @param list<LlmModelOptionsCacheWriteCostList>|null  $cacheWriteCostList   Per-cache-TTL cache-write pricing; overrides cache_write_cost per TTL.
     * @param LlmModelOptionsCohere|null                    $cohere
     * @param list<LlmModelOptionsContextWindowFactor>|null $contextWindowFactor  Above an input-token threshold, scale input/output pricing with the corresponding factor.
     * @param LlmModelOptionsDashscope|null                 $dashscope
     * @param LlmModelOptionsDatabricks|null                $databricks
     * @param int|null                                      $embeddingsDimensions If using embeddings models, set the number of dimensions to generate.
     * @param LlmModelOptionsGemini|null                    $gemini
     * @param LlmModelOptionsHuggingface|null               $huggingface
     * @param int|float|null                                $inputCost            Defines the cost per 1M tokens in your prompt.
     * @param LlmModelOptionsLlama2Format|null              $llama2Format         If using llama2 provider, select the upstream message format.
     * @param int|null                                      $maxTokens            Defines the max_tokens, if using chat or completion models.
     * @param LlmModelOptionsMistralFormat|null             $mistralFormat        If using mistral provider, select the upstream message format.
     * @param int|float|null                                $outputCost           Defines the cost per 1M tokens in the output of the AI.
     * @param list<LlmModelOptionsServiceTierFactor>|null   $serviceTierFactor    Multiplier applied to the whole request for a service tier.
     * @param int|float|null                                $temperature          Defines the matching temperature, if using chat or completion models.
     * @param int|null                                      $topK                 Defines the top-k most likely tokens, if supported.
     * @param int|float|null                                $topP                 Defines the top-p probability mass, if supported.
     * @param string|null                                   $upstreamPath         Manually specify or override the AI operation path, used when e.g.
     * @param string|null                                   $upstreamUrl          Manually specify or override the full URL to the AI operation endpoints, when calling (self-)hosted models, o…
     */
    public function __construct(
        public ?string $anthropicVersion = null,
        public ?string $azureApiVersion = null,
        public ?string $azureDeploymentId = null,
        public ?string $azureInstance = null,
        public ?LlmModelOptionsBedrock $bedrock = null,
        public int|float|null $cacheReadCost = null,
        public int|float|null $cacheWriteCost = null,
        public ?array $cacheWriteCostList = null,
        public ?LlmModelOptionsCohere $cohere = null,
        public ?array $contextWindowFactor = null,
        public ?LlmModelOptionsDashscope $dashscope = null,
        public ?LlmModelOptionsDatabricks $databricks = null,
        public ?int $embeddingsDimensions = null,
        public ?LlmModelOptionsGemini $gemini = null,
        public ?LlmModelOptionsHuggingface $huggingface = null,
        public int|float|null $inputCost = null,
        public ?LlmModelOptionsLlama2Format $llama2Format = null,
        public ?int $maxTokens = null,
        public ?LlmModelOptionsMistralFormat $mistralFormat = null,
        public int|float|null $outputCost = null,
        public ?array $serviceTierFactor = null,
        public int|float|null $temperature = null,
        public ?int $topK = null,
        public int|float|null $topP = null,
        public ?string $upstreamPath = null,
        public ?string $upstreamUrl = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $bedrock = Data::mapOrNull($data, 'bedrock');
        $cacheWriteCostList = Data::listOfMapsOrNull($data, 'cache_write_cost_list');
        $cohere = Data::mapOrNull($data, 'cohere');
        $contextWindowFactor = Data::listOfMapsOrNull($data, 'context_window_factor');
        $dashscope = Data::mapOrNull($data, 'dashscope');
        $databricks = Data::mapOrNull($data, 'databricks');
        $gemini = Data::mapOrNull($data, 'gemini');
        $huggingface = Data::mapOrNull($data, 'huggingface');
        $serviceTierFactor = Data::listOfMapsOrNull($data, 'service_tier_factor');

        return new self(
            anthropicVersion: Data::stringOrNull($data, 'anthropic_version'),
            azureApiVersion: Data::stringOrNull($data, 'azure_api_version'),
            azureDeploymentId: Data::stringOrNull($data, 'azure_deployment_id'),
            azureInstance: Data::stringOrNull($data, 'azure_instance'),
            bedrock: $bedrock === null ? null : LlmModelOptionsBedrock::fromArray($bedrock),
            cacheReadCost: Data::numberOrNull($data, 'cache_read_cost'),
            cacheWriteCost: Data::numberOrNull($data, 'cache_write_cost'),
            cacheWriteCostList: $cacheWriteCostList === null ? null : array_map(LlmModelOptionsCacheWriteCostList::fromArray(...), $cacheWriteCostList),
            cohere: $cohere === null ? null : LlmModelOptionsCohere::fromArray($cohere),
            contextWindowFactor: $contextWindowFactor === null ? null : array_map(LlmModelOptionsContextWindowFactor::fromArray(...), $contextWindowFactor),
            dashscope: $dashscope === null ? null : LlmModelOptionsDashscope::fromArray($dashscope),
            databricks: $databricks === null ? null : LlmModelOptionsDatabricks::fromArray($databricks),
            embeddingsDimensions: Data::intOrNull($data, 'embeddings_dimensions'),
            gemini: $gemini === null ? null : LlmModelOptionsGemini::fromArray($gemini),
            huggingface: $huggingface === null ? null : LlmModelOptionsHuggingface::fromArray($huggingface),
            inputCost: Data::numberOrNull($data, 'input_cost'),
            llama2Format: Data::enumOrNull($data, 'llama2_format', LlmModelOptionsLlama2Format::class),
            maxTokens: Data::intOrNull($data, 'max_tokens'),
            mistralFormat: Data::enumOrNull($data, 'mistral_format', LlmModelOptionsMistralFormat::class),
            outputCost: Data::numberOrNull($data, 'output_cost'),
            serviceTierFactor: $serviceTierFactor === null ? null : array_map(LlmModelOptionsServiceTierFactor::fromArray(...), $serviceTierFactor),
            temperature: Data::numberOrNull($data, 'temperature'),
            topK: Data::intOrNull($data, 'top_k'),
            topP: Data::numberOrNull($data, 'top_p'),
            upstreamPath: Data::stringOrNull($data, 'upstream_path'),
            upstreamUrl: Data::stringOrNull($data, 'upstream_url'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'anthropic_version' => $this->anthropicVersion,
            'azure_api_version' => $this->azureApiVersion,
            'azure_deployment_id' => $this->azureDeploymentId,
            'azure_instance' => $this->azureInstance,
            'bedrock' => $this->bedrock?->toArray(),
            'cache_read_cost' => $this->cacheReadCost,
            'cache_write_cost' => $this->cacheWriteCost,
            'cache_write_cost_list' => Data::toArrays($this->cacheWriteCostList),
            'cohere' => $this->cohere?->toArray(),
            'context_window_factor' => Data::toArrays($this->contextWindowFactor),
            'dashscope' => $this->dashscope?->toArray(),
            'databricks' => $this->databricks?->toArray(),
            'embeddings_dimensions' => $this->embeddingsDimensions,
            'gemini' => $this->gemini?->toArray(),
            'huggingface' => $this->huggingface?->toArray(),
            'input_cost' => $this->inputCost,
            'llama2_format' => $this->llama2Format?->value,
            'max_tokens' => $this->maxTokens,
            'mistral_format' => $this->mistralFormat?->value,
            'output_cost' => $this->outputCost,
            'service_tier_factor' => Data::toArrays($this->serviceTierFactor),
            'temperature' => $this->temperature,
            'top_k' => $this->topK,
            'top_p' => $this->topP,
            'upstream_path' => $this->upstreamPath,
            'upstream_url' => $this->upstreamUrl,
        ]);
    }
}
