<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `ordering` object of Plugin (spec `Plugin.ordering`).
 */
#[Schema('#/components/schemas/Plugin/properties/ordering')]
final readonly class PluginOrdering implements Model
{
    /**
     * @param PluginOrderingPhases|null $after
     * @param PluginOrderingPhases|null $before
     */
    public function __construct(
        public ?PluginOrderingPhases $after = null,
        public ?PluginOrderingPhases $before = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $after = Data::mapOrNull($data, 'after');
        $before = Data::mapOrNull($data, 'before');

        return new self(
            after: $after === null ? null : PluginOrderingPhases::fromArray($after),
            before: $before === null ? null : PluginOrderingPhases::fromArray($before),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'after' => $this->after?->toArray(),
            'before' => $this->before?->toArray(),
        ]);
    }
}
