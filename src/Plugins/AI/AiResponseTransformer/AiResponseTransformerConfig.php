<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiResponseTransformer;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `ai-response-transformer` plugin (AI Response Transformer; doc `AI/ai-response-transformer.md`).
 *
 * Read it from a returned Plugin with `AiResponseTransformerConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('AI/ai-response-transformer.md', '#/properties/config')]
final readonly class AiResponseTransformerConfig implements PluginConfig
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'ai-response-transformer';

    /**
     * @param Llm         $llm
     * @param string      $prompt                           Use this prompt to tune the LLM system/assistant message for the returning proxy response (from the upstream)…
     * @param string|null $httpProxyHost                    A string representing a host name, such as example.com.
     * @param int|null    $httpProxyPort                    An integer representing a port number between 0 and 65535, inclusive.
     * @param int|null    $httpTimeout                      Timeout in milliseconds for the AI upstream service. Default: `60000`.
     * @param string|null $httpsProxyHost                   A string representing a host name, such as example.com.
     * @param int|null    $httpsProxyPort                   An integer representing a port number between 0 and 65535, inclusive.
     * @param bool|null   $httpsVerify                      Verify the TLS certificate of the AI upstream service. Default: `true`.
     * @param int|null    $maxRequestBodySize               max allowed body size allowed to be introspected. Default: `1048576`.
     * @param bool|null   $parseLlmResponseJsonInstructions Set true to read specific response format from the LLM, and accordingly set the status code / body / headers… Default: `false`.
     * @param string|null $transformationExtractPattern     Defines the regular expression that must match to indicate a successful AI transformation at the response pha…
     */
    public function __construct(
        public Llm $llm,
        public string $prompt,
        public ?string $httpProxyHost = null,
        public ?int $httpProxyPort = null,
        public ?int $httpTimeout = null,
        public ?string $httpsProxyHost = null,
        public ?int $httpsProxyPort = null,
        public ?bool $httpsVerify = null,
        public ?int $maxRequestBodySize = null,
        public ?bool $parseLlmResponseJsonInstructions = null,
        public ?string $transformationExtractPattern = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            llm: Llm::fromArray(Data::map($data, 'llm')),
            prompt: Data::string($data, 'prompt'),
            httpProxyHost: Data::stringOrNull($data, 'http_proxy_host'),
            httpProxyPort: Data::intOrNull($data, 'http_proxy_port'),
            httpTimeout: Data::intOrNull($data, 'http_timeout'),
            httpsProxyHost: Data::stringOrNull($data, 'https_proxy_host'),
            httpsProxyPort: Data::intOrNull($data, 'https_proxy_port'),
            httpsVerify: Data::boolOrNull($data, 'https_verify'),
            maxRequestBodySize: Data::intOrNull($data, 'max_request_body_size'),
            parseLlmResponseJsonInstructions: Data::boolOrNull($data, 'parse_llm_response_json_instructions'),
            transformationExtractPattern: Data::stringOrNull($data, 'transformation_extract_pattern'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'llm' => $this->llm->toArray(),
            'prompt' => $this->prompt,
            'http_proxy_host' => $this->httpProxyHost,
            'http_proxy_port' => $this->httpProxyPort,
            'http_timeout' => $this->httpTimeout,
            'https_proxy_host' => $this->httpsProxyHost,
            'https_proxy_port' => $this->httpsProxyPort,
            'https_verify' => $this->httpsVerify,
            'max_request_body_size' => $this->maxRequestBodySize,
            'parse_llm_response_json_instructions' => $this->parseLlmResponseJsonInstructions,
            'transformation_extract_pattern' => $this->transformationExtractPattern,
        ]);
    }

    /**
     * Reads the typed configuration of a `ai-response-transformer` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `ai-response-transformer` plugin
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
