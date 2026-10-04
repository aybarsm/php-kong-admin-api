<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `partials[]` object of Plugin (spec `Plugin.partials[]`).
 */
#[Schema('#/components/schemas/Plugin/properties/partials')]
final readonly class PluginPartial implements Model
{
    /**
     * @param string|null $id   A string representing a UUID (universally unique identifier).
     * @param string|null $name A unique string representing a UTF-8 encoded name.
     * @param string|null $path
     */
    public function __construct(
        public ?string $id = null,
        public ?string $name = null,
        public ?string $path = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            id: Data::stringOrNull($data, 'id'),
            name: Data::stringOrNull($data, 'name'),
            path: Data::stringOrNull($data, 'path'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'id' => $this->id,
            'name' => $this->name,
            'path' => $this->path,
        ]);
    }
}
