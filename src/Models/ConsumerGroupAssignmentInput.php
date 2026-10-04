<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Request body for adding a Consumer to a Consumer Group (`POST /consumers/{ConsumerIdOrUsername}/consumer_groups`).
 */
#[Schema('#/paths/~1consumers~1{ConsumerIdOrUsername}~1consumer_groups/post/requestBody/content/application~1json/schema')]
final readonly class ConsumerGroupAssignmentInput implements Input
{
    /**
     * @param string|null $group
     */
    public function __construct(
        public ?string $group = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'group' => $this->group,
        ]);
    }
}
