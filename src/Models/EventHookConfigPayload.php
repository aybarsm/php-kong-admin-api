<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.payload` object of EventHook (spec `#/components/schemas/Event-Hooks/properties/data.config.payload`).
 */
#[Schema('#/components/schemas/Event-Hooks/properties/data/items/properties/config/properties/payload')]
final readonly class EventHookConfigPayload implements Model
{
    /**
     * @param string|null $text
     */
    public function __construct(
        public ?string $text = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            text: Data::stringOrNull($data, 'text'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'text' => $this->text,
        ]);
    }
}
