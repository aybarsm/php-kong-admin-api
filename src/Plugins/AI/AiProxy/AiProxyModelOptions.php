<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiProxy;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.model.options` object of the AI Proxy plugin (doc `AI/ai-proxy.md`).
 */
#[PluginSchema('AI/ai-proxy.md', '#/properties/config/properties/model/properties/options')]
final readonly class AiProxyModelOptions implements Model
{
    /**
     * @param string|null                                       $anthropicVersion     Defines the schema/API version, if using Anthropic provider.
     * @param string|null                                       $azureApiVersion      'api-version' for Azure OpenAI instances. Default: `2023-05-15`.
     * @param string|null                                       $azureDeploymentId    Deployment ID for Azure OpenAI instances.
     * @param string|null                                       $azureInstance        Instance name for Azure OpenAI hosted models.
     * @param AiProxyModelOptionsBedrock|null                   $bedrock
     * @param float|null                                        $cacheReadCost        Defines the cost per 1M cache-read (cached) prompt tokens.
     * @param float|null                                        $cacheWriteCost       Defines the cost per 1M cache-write prompt tokens.
     * @param list<AiProxyModelOptionsCacheWriteCostList>|null  $cacheWriteCostList   Per-cache-TTL cache-write pricing; overrides cache_write_cost per TTL.
     * @param AiProxyModelOptionsCohere|null                    $cohere
     * @param list<AiProxyModelOptionsContextWindowFactor>|null $contextWindowFactor  Above an input-token threshold, scale input/output pricing with the corresponding factor.
     * @param AiProxyModelOptionsDashscope|null                 $dashscope
     * @param AiProxyModelOptionsDatabricks|null                $databricks
     * @param int|null                                          $embeddingsDimensions If using embeddings models, set the number of dimensions to generate.
     * @param AiProxyModelOptionsGemini|null                    $gemini
     * @param AiProxyModelOptionsHuggingface|null               $huggingface
     * @param float|null                                        $inputCost            Defines the cost per 1M tokens in your prompt.
     * @param AiProxyModelOptionsLlama2Format|null              $llama2Format         If using llama2 provider, select the upstream message format.
     * @param int|null                                          $maxTokens            Defines the max_tokens, if using chat or completion models.
     * @param AiProxyModelOptionsMistralFormat|null             $mistralFormat        If using mistral provider, select the upstream message format.
     * @param float|null                                        $outputCost           Defines the cost per 1M tokens in the output of the AI.
     * @param list<AiProxyModelOptionsServiceTierFactor>|null   $serviceTierFactor    Multiplier applied to the whole request for a service tier.
     * @param float|null                                        $temperature          Defines the matching temperature, if using chat or completion models.
     * @param int|null                                          $topK                 Defines the top-k most likely tokens, if supported.
     * @param float|null                                        $topP                 Defines the top-p probability mass, if supported.
     * @param string|null                                       $upstreamPath         Manually specify or override the AI operation path, used when e.g.
     * @param string|null                                       $upstreamUrl          Manually specify or override the full URL to the AI operation endpoints, when calling (self-)hosted models, o…
     */
    public function __construct(
        public ?string $anthropicVersion = null,
        public ?string $azureApiVersion = null,
        public ?string $azureDeploymentId = null,
        public ?string $azureInstance = null,
        public ?AiProxyModelOptionsBedrock $bedrock = null,
        public ?float $cacheReadCost = null,
        public ?float $cacheWriteCost = null,
        public ?array $cacheWriteCostList = null,
        public ?AiProxyModelOptionsCohere $cohere = null,
        public ?array $contextWindowFactor = null,
        public ?AiProxyModelOptionsDashscope $dashscope = null,
        public ?AiProxyModelOptionsDatabricks $databricks = null,
        public ?int $embeddingsDimensions = null,
        public ?AiProxyModelOptionsGemini $gemini = null,
        public ?AiProxyModelOptionsHuggingface $huggingface = null,
        public ?float $inputCost = null,
        public ?AiProxyModelOptionsLlama2Format $llama2Format = null,
        public ?int $maxTokens = null,
        public ?AiProxyModelOptionsMistralFormat $mistralFormat = null,
        public ?float $outputCost = null,
        public ?array $serviceTierFactor = null,
        public ?float $temperature = null,
        public ?int $topK = null,
        public ?float $topP = null,
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
            bedrock: $bedrock === null ? null : AiProxyModelOptionsBedrock::fromArray($bedrock),
            cacheReadCost: Data::floatOrNull($data, 'cache_read_cost'),
            cacheWriteCost: Data::floatOrNull($data, 'cache_write_cost'),
            cacheWriteCostList: $cacheWriteCostList === null ? null : array_map(AiProxyModelOptionsCacheWriteCostList::fromArray(...), $cacheWriteCostList),
            cohere: $cohere === null ? null : AiProxyModelOptionsCohere::fromArray($cohere),
            contextWindowFactor: $contextWindowFactor === null ? null : array_map(AiProxyModelOptionsContextWindowFactor::fromArray(...), $contextWindowFactor),
            dashscope: $dashscope === null ? null : AiProxyModelOptionsDashscope::fromArray($dashscope),
            databricks: $databricks === null ? null : AiProxyModelOptionsDatabricks::fromArray($databricks),
            embeddingsDimensions: Data::intOrNull($data, 'embeddings_dimensions'),
            gemini: $gemini === null ? null : AiProxyModelOptionsGemini::fromArray($gemini),
            huggingface: $huggingface === null ? null : AiProxyModelOptionsHuggingface::fromArray($huggingface),
            inputCost: Data::floatOrNull($data, 'input_cost'),
            llama2Format: Data::enumOrNull($data, 'llama2_format', AiProxyModelOptionsLlama2Format::class),
            maxTokens: Data::intOrNull($data, 'max_tokens'),
            mistralFormat: Data::enumOrNull($data, 'mistral_format', AiProxyModelOptionsMistralFormat::class),
            outputCost: Data::floatOrNull($data, 'output_cost'),
            serviceTierFactor: $serviceTierFactor === null ? null : array_map(AiProxyModelOptionsServiceTierFactor::fromArray(...), $serviceTierFactor),
            temperature: Data::floatOrNull($data, 'temperature'),
            topK: Data::intOrNull($data, 'top_k'),
            topP: Data::floatOrNull($data, 'top_p'),
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
