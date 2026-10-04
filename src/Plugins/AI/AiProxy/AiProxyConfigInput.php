<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiProxy;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `ai-proxy` plugin (AI Proxy; doc `AI/ai-proxy.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('AI/ai-proxy.md', '#/properties/config')]
final readonly class AiProxyConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'ai-proxy';

    /**
     * @param AiProxyModel|null             $model              Required by the plugin doc.
     * @param AiProxyRouteType|null         $routeType          The model's operation implementation, for this provider. Required by the plugin doc.
     * @param AiProxyAuth|null              $auth
     * @param AiProxyGenaiCategory|null     $genaiCategory      Generative AI category of the request Default: `text/generation`.
     * @param AiProxyLlmFormat|null         $llmFormat          LLM input and output format and schema to use Default: `openai`.
     * @param AiProxyLogging|null           $logging
     * @param int|null                      $maxRequestBodySize max allowed body size allowed to be introspected. Default: `1048576`.
     * @param bool|null                     $modelNameHeader    Display the model name selected in the X-Kong-LLM-Model response header Default: `true`.
     * @param AiProxyResponseStreaming|null $responseStreaming  Whether to 'optionally allow', 'deny', or 'always' (force) the streaming of answers via server sent events. Default: `allow`.
     */
    public function __construct(
        public ?AiProxyModel $model = null,
        public ?AiProxyRouteType $routeType = null,
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
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'model' => $this->model?->toArray(),
            'route_type' => $this->routeType?->value,
            'auth' => $this->auth?->toArray(),
            'genai_category' => $this->genaiCategory?->value,
            'llm_format' => $this->llmFormat?->value,
            'logging' => $this->logging?->toArray(),
            'max_request_body_size' => $this->maxRequestBodySize,
            'model_name_header' => $this->modelNameHeader,
            'response_streaming' => $this->responseStreaming?->value,
        ]);
    }
}
