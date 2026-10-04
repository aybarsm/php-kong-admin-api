<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `ordering.before` object of Plugin (spec `Plugin.ordering.before`).
 */
#[Schema('#/components/schemas/Plugin/properties/ordering/properties/before')]
final readonly class PluginOrderingPhases implements Model
{
    /**
     * @param list<string>|null $access
     */
    public function __construct(
        public ?array $access = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            access: Data::stringListOrNull($data, 'access'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'access' => $this->access,
        ]);
    }
}
