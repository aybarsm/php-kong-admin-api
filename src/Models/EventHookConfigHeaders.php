<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.headers` object of EventHook (spec `#/components/schemas/Event-Hooks/properties/data.config.headers`).
 */
#[Schema('#/components/schemas/Event-Hooks/properties/data/items/properties/config/properties/headers')]
final readonly class EventHookConfigHeaders implements Model
{
    /**
     * @param string|null $contentType
     */
    public function __construct(
        public ?string $contentType = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            contentType: Data::stringOrNull($data, 'content-type'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'content-type' => $this->contentType,
        ]);
    }
}
