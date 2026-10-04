<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\AI\AiPromptTemplate;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `ai-prompt-template` plugin (AI Prompt Template; doc `AI/ai-prompt-template.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('AI/ai-prompt-template.md', '#/properties/config')]
final readonly class AiPromptTemplateConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'ai-prompt-template';

    /**
     * @param list<Templates>|null $templates                Array of templates available to the request context. Required by the plugin doc.
     * @param bool|null            $allowUntemplatedRequests Set true to allow requests that don't call or match any template. Default: `true`.
     * @param bool|null            $logOriginalRequest       Set true to add the original request to the Kong log plugin(s) output. Default: `false`.
     * @param int|null             $maxRequestBodySize       max allowed body size allowed to be introspected. Default: `1048576`.
     */
    public function __construct(
        public ?array $templates = null,
        public ?bool $allowUntemplatedRequests = null,
        public ?bool $logOriginalRequest = null,
        public ?int $maxRequestBodySize = null,
    ) {
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
}
