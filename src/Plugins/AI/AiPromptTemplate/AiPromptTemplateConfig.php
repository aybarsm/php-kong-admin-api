<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiPromptTemplate;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `ai-prompt-template` plugin (AI Prompt Template; doc `AI/ai-prompt-template.md`).
 *
 * Read it from a returned Plugin with `AiPromptTemplateConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('AI/ai-prompt-template.md', '#/properties/config')]
final readonly class AiPromptTemplateConfig implements PluginConfig
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'ai-prompt-template';

    /**
     * @param list<AiPromptTemplateTemplates> $templates                Array of templates available to the request context.
     * @param bool|null                       $allowUntemplatedRequests Set true to allow requests that don't call or match any template. Default: `true`.
     * @param bool|null                       $logOriginalRequest       Set true to add the original request to the Kong log plugin(s) output. Default: `false`.
     * @param int|null                        $maxRequestBodySize       max allowed body size allowed to be introspected. Default: `1048576`.
     */
    public function __construct(
        public array $templates,
        public ?bool $allowUntemplatedRequests = null,
        public ?bool $logOriginalRequest = null,
        public ?int $maxRequestBodySize = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            templates: array_map(AiPromptTemplateTemplates::fromArray(...), Data::listOfMaps($data, 'templates')),
            allowUntemplatedRequests: Data::boolOrNull($data, 'allow_untemplated_requests'),
            logOriginalRequest: Data::boolOrNull($data, 'log_original_request'),
            maxRequestBodySize: Data::intOrNull($data, 'max_request_body_size'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'templates' => Data::toArrays($this->templates),
            'allow_untemplated_requests' => $this->allowUntemplatedRequests,
            'log_original_request' => $this->logOriginalRequest,
            'max_request_body_size' => $this->maxRequestBodySize,
        ]);
    }

    /**
     * Reads the typed configuration of a `ai-prompt-template` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `ai-prompt-template` plugin
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
