<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The response of adding a Consumer to a Consumer Group (`POST /consumer_groups/{ConsumerGroupId}/consumers`).
 */
#[Schema('#/paths/~1consumer_groups~1{ConsumerGroupId}~1consumers/post/responses/201/content/application~1json/schema')]
final readonly class ConsumerGroupMembership implements Model
{
    /**
     * @param ConsumerGroup|null  $consumerGroup
     * @param list<Consumer>|null $consumers
     */
    public function __construct(
        public ?ConsumerGroup $consumerGroup = null,
        public ?array $consumers = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $consumerGroup = Data::mapOrNull($data, 'consumer_group');
        $consumers = Data::listOfMapsOrNull($data, 'consumers');

        return new self(
            consumerGroup: $consumerGroup === null ? null : ConsumerGroup::fromArray($consumerGroup),
            consumers: $consumers === null ? null : array_map(Consumer::fromArray(...), $consumers),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'consumer_group' => $this->consumerGroup?->toArray(),
            'consumers' => Data::toArrays($this->consumers),
        ]);
    }
}
