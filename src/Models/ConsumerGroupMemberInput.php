<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Request body for adding a Consumer to a Consumer Group (`POST /consumer_groups/{ConsumerGroupId}/consumers`).
 */
#[Schema('#/paths/~1consumer_groups~1{ConsumerGroupId}~1consumers/post/requestBody/content/application~1json/schema')]
final readonly class ConsumerGroupMemberInput implements Input
{
    /**
     * @param string|null $consumer
     */
    public function __construct(
        public ?string $consumer = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'consumer' => $this->consumer,
        ]);
    }
}
