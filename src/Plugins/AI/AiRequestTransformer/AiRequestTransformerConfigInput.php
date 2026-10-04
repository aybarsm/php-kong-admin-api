<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiRequestTransformer;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `ai-request-transformer` plugin (AI Request Transformer; doc `AI/ai-request-transformer.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('AI/ai-request-transformer.md', '#/properties/config')]
final readonly class AiRequestTransformerConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'ai-request-transformer';

    /**
     * @param Llm|null    $llm                          Required by the plugin doc.
     * @param string|null $prompt                       Use this prompt to tune the LLM system/assistant message for the incoming proxy request (from the client), an… Required by the plugin doc.
     * @param string|null $httpProxyHost                A string representing a host name, such as example.com.
     * @param int|null    $httpProxyPort                An integer representing a port number between 0 and 65535, inclusive.
     * @param int|null    $httpTimeout                  Timeout in milliseconds for the AI upstream service. Default: `60000`.
     * @param string|null $httpsProxyHost               A string representing a host name, such as example.com.
     * @param int|null    $httpsProxyPort               An integer representing a port number between 0 and 65535, inclusive.
     * @param bool|null   $httpsVerify                  Verify the TLS certificate of the AI upstream service. Default: `true`.
     * @param int|null    $maxRequestBodySize           max allowed body size allowed to be introspected. Default: `1048576`.
     * @param string|null $transformationExtractPattern Defines the regular expression that must match to indicate a successful AI transformation at the request phas…
     */
    public function __construct(
        public ?Llm $llm = null,
        public ?string $prompt = null,
        public ?string $httpProxyHost = null,
        public ?int $httpProxyPort = null,
        public ?int $httpTimeout = null,
        public ?string $httpsProxyHost = null,
        public ?int $httpsProxyPort = null,
        public ?bool $httpsVerify = null,
        public ?int $maxRequestBodySize = null,
        public ?string $transformationExtractPattern = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'llm' => $this->llm?->toArray(),
            'prompt' => $this->prompt,
            'http_proxy_host' => $this->httpProxyHost,
            'http_proxy_port' => $this->httpProxyPort,
            'http_timeout' => $this->httpTimeout,
            'https_proxy_host' => $this->httpsProxyHost,
            'https_proxy_port' => $this->httpsProxyPort,
            'https_verify' => $this->httpsVerify,
            'max_request_body_size' => $this->maxRequestBodySize,
            'transformation_extract_pattern' => $this->transformationExtractPattern,
        ]);
    }
}
