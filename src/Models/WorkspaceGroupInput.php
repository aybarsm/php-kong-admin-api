<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Request body for creating or renaming a workspace group (spec request body `UpdateGroupsRequest`).
 */
#[Schema('#/components/requestBodies/UpdateGroupsRequest/content/application~1json/schema')]
final readonly class WorkspaceGroupInput implements Input
{
    /**
     * @param string|null $name
     */
    public function __construct(
        public ?string $name = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'name' => $this->name,
        ]);
    }
}
