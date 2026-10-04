<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The response of adding a Consumer to Consumer Groups (`POST /consumers/{ConsumerIdOrUsername}/consumer_groups`).
 */
#[Schema('#/paths/~1consumers~1{ConsumerIdOrUsername}~1consumer_groups/post/responses/201/content/application~1json/schema')]
final readonly class ConsumerMembership implements Model
{
    /**
     * @param Consumer|null            $consumer
     * @param list<ConsumerGroup>|null $consumerGroups
     */
    public function __construct(
        public ?Consumer $consumer = null,
        public ?array $consumerGroups = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $consumer = Data::mapOrNull($data, 'consumer');
        $consumerGroups = Data::listOfMapsOrNull($data, 'consumer_groups');

        return new self(
            consumer: $consumer === null ? null : Consumer::fromArray($consumer),
            consumerGroups: $consumerGroups === null ? null : array_map(ConsumerGroup::fromArray(...), $consumerGroups),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'consumer' => $this->consumer?->toArray(),
            'consumer_groups' => Data::toArrays($this->consumerGroups),
        ]);
    }
}
