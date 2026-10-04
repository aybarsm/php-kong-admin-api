<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Transformation\ResponseTransformer;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.add` object of the Response Transformer plugin (doc `Transformation/response-transformer.md`).
 */
#[PluginSchema('Transformation/response-transformer.md', '#/properties/config/properties/add')]
final readonly class Add implements Model
{
    /**
     * @param list<string>|null       $headers   Default: `[]`.
     * @param list<string>|null       $json      Default: `[]`.
     * @param list<AddJsonTypes>|null $jsonTypes List of JSON type names. Default: `[]`.
     */
    public function __construct(
        public ?array $headers = null,
        public ?array $json = null,
        public ?array $jsonTypes = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            headers: Data::stringListOrNull($data, 'headers'),
            json: Data::stringListOrNull($data, 'json'),
            jsonTypes: Data::enumListOrNull($data, 'json_types', AddJsonTypes::class),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'headers' => $this->headers,
            'json' => $this->json,
            'json_types' => Data::enumValues($this->jsonTypes),
        ]);
    }
}
