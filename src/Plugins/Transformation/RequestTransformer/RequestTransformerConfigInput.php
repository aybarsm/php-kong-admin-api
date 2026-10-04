<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Transformation\RequestTransformer;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `request-transformer` plugin (Request Transformer; doc `Transformation/request-transformer.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('Transformation/request-transformer.md', '#/properties/config')]
final readonly class RequestTransformerConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'request-transformer';

    /**
     * @param Add|null     $add
     * @param Append|null  $append
     * @param string|null  $httpMethod A string representing an HTTP method, such as GET, POST, PUT, or DELETE.
     * @param Remove|null  $remove
     * @param Rename|null  $rename
     * @param Replace|null $replace
     */
    public function __construct(
        public ?Add $add = null,
        public ?Append $append = null,
        public ?string $httpMethod = null,
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
            'http_method' => $this->httpMethod,
            'remove' => $this->remove?->toArray(),
            'rename' => $this->rename?->toArray(),
            'replace' => $this->replace?->toArray(),
        ]);
    }
}
