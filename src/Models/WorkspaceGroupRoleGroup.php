<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `group` object of WorkspaceGroupRole (spec `#/components/responses/GetRolesResponse/content/application~1json/schema.group`).
 */
#[Schema('#/components/responses/GetRolesResponse/content/application~1json/schema/items/properties/group')]
final readonly class WorkspaceGroupRoleGroup implements Model
{
    /**
     * @param string|null $id
     * @param string|null $name
     */
    public function __construct(
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
            'id' => $this->id,
            'name' => $this->name,
        ]);
    }
}
