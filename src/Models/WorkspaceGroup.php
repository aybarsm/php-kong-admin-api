<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * A group listed by `GET /workspace_/groups` (spec response `ListAllGroups`).
 *
 * `POST /workspace_/groups` returns the same properties (spec response `CreateGroupsResponse`).
 */
#[Schema('#/components/responses/ListAllGroups/content/application~1json/schema')]
final readonly class WorkspaceGroup implements Model
{
    /**
     * @param string|null $createdAt
     * @param string|null $id
     * @param string|null $name
     */
    public function __construct(
        public ?string $createdAt = null,
        public ?string $id = null,
        public ?string $name = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            createdAt: Data::stringOrNull($data, 'created_at'),
            id: Data::stringOrNull($data, 'id'),
            name: Data::stringOrNull($data, 'name'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'name' => $this->name,
        ]);
    }
}
