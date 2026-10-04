<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Transformation\ResponseTransformer;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `response-transformer` plugin (Response Transformer; doc `Transformation/response-transformer.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('Transformation/response-transformer.md', '#/properties/config')]
final readonly class ResponseTransformerConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'response-transformer';

    /**
     * @param Add|null     $add
     * @param Append|null  $append
     * @param Remove|null  $remove
     * @param Rename|null  $rename
     * @param Replace|null $replace
     */
    public function __construct(
        public ?Add $add = null,
        public ?Append $append = null,
        public ?Remove $remove = null,
        public ?Rename $rename = null,
        public ?Replace $replace = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'add' => $this->add?->toArray(),
            'append' => $this->append?->toArray(),
            'remove' => $this->remove?->toArray(),
            'rename' => $this->rename?->toArray(),
            'replace' => $this->replace?->toArray(),
        ]);
    }
}
