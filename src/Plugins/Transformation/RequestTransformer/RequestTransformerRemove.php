<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Transformation\RequestTransformer;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.remove` object of the Request Transformer plugin (doc `Transformation/request-transformer.md`).
 */
#[PluginSchema('Transformation/request-transformer.md', '#/properties/config/properties/remove')]
final readonly class RequestTransformerRemove implements Model
{
    /**
     * @param list<string>|null $body        Default: `[]`.
     * @param list<string>|null $headers     Default: `[]`.
     * @param list<string>|null $querystring Default: `[]`.
     */
    public function __construct(
        public ?array $body = null,
        public ?array $headers = null,
        public ?array $querystring = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            body: Data::stringListOrNull($data, 'body'),
            headers: Data::stringListOrNull($data, 'headers'),
            querystring: Data::stringListOrNull($data, 'querystring'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'body' => $this->body,
            'headers' => $this->headers,
            'querystring' => $this->querystring,
        ]);
    }
}
