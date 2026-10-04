<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `meta` object of Workspace (spec `Workspace.meta`).
 */
#[Schema('#/components/schemas/Workspace/properties/meta')]
final readonly class WorkspaceMeta implements Model
{
    /**
     * @param string|null $color
     * @param string|null $thumbnail
     */
    public function __construct(
        public ?string $color = null,
        public ?string $thumbnail = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            color: Data::stringOrNull($data, 'color'),
            thumbnail: Data::stringOrNull($data, 'thumbnail'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'color' => $this->color,
            'thumbnail' => $this->thumbnail,
        ]);
    }
}
