<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiProxy;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `ai-proxy` plugin (AI Proxy; doc `AI/ai-proxy.md`).
 *
 * Read it from a returned Plugin with `AiProxyConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('AI/ai-proxy.md', '#/properties/config')]
final readonly class AiProxyConfig implements PluginConfig
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'ai-proxy';

    /**
     * @param AiProxyModel                  $model
     * @param AiProxyRouteType              $routeType          The model's operation implementation, for this provider.
     * @param AiProxyAuth|null              $auth
     * @param AiProxyGenaiCategory|null     $genaiCategory      Generative AI category of the request Default: `text/generation`.
     * @param AiProxyLlmFormat|null         $llmFormat          LLM input and output format and schema to use Default: `openai`.
     * @param AiProxyLogging|null           $logging
     * @param int|null                      $maxRequestBodySize max allowed body size allowed to be introspected. Default: `1048576`.
     * @param bool|null                     $modelNameHeader    Display the model name selected in the X-Kong-LLM-Model response header Default: `true`.
     * @param AiProxyResponseStreaming|null $responseStreaming  Whether to 'optionally allow', 'deny', or 'always' (force) the streaming of answers via server sent events. Default: `allow`.
     */
    public function __construct(
        public AiProxyModel $model,
        public AiProxyRouteType $routeType,
        public ?AiProxyAuth $auth = null,
        public ?AiProxyGenaiCategory $genaiCategory = null,
        public ?AiProxyLlmFormat $llmFormat = null,
        public ?AiProxyLogging $logging = null,
        public ?int $maxRequestBodySize = null,
        public ?bool $modelNameHeader = null,
        public ?AiProxyResponseStreaming $responseStreaming = null,
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
            model: AiProxyModel::fromArray(Data::map($data, 'model')),
            routeType: Data::enum($data, 'route_type', AiProxyRouteType::class),
            auth: $auth === null ? null : AiProxyAuth::fromArray($auth),
            genaiCategory: Data::enumOrNull($data, 'genai_category', AiProxyGenaiCategory::class),
            llmFormat: Data::enumOrNull($data, 'llm_format', AiProxyLlmFormat::class),
            logging: $logging === null ? null : AiProxyLogging::fromArray($logging),
            maxRequestBodySize: Data::intOrNull($data, 'max_request_body_size'),
            modelNameHeader: Data::boolOrNull($data, 'model_name_header'),
            responseStreaming: Data::enumOrNull($data, 'response_streaming', AiProxyResponseStreaming::class),
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
            'genai_category' => $this->genaiCategory?->value,
            'llm_format' => $this->llmFormat?->value,
            'logging' => $this->logging?->toArray(),
            'max_request_body_size' => $this->maxRequestBodySize,
            'model_name_header' => $this->modelNameHeader,
            'response_streaming' => $this->responseStreaming?->value,
        ]);
    }

    /**
     * Reads the typed configuration of a `ai-proxy` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `ai-proxy` plugin
     * @throws UnexpectedResponseException when its `config` does not match the plugin doc
     */
    #[Override]
    public static function fromPlugin(Plugin $plugin): static
    {
        if ($plugin->name !== self::NAME) {
            throw new InvalidArgumentException(sprintf('Expected a "%s" plugin, got "%s".', self::NAME, $plugin->name));
        }

        return self::fromArray(Data::asMap($plugin->config ?? [], 'config'));
    }
}
