<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The response of `GET /consumer_groups/{ConsumerGroupId}` (spec schema `ConsumerGroupInsideWrapper`).
 *
 * The spec wraps the group in `consumer_group` and defines no other field, also when `list_consumers` is
 * sent (spec-notes Q9).
 */
#[Schema('ConsumerGroupInsideWrapper')]
final readonly class ConsumerGroupInsideWrapper implements Model
{
    /**
     * @param ConsumerGroup|null $consumerGroup
     */
    public function __construct(
        public ?ConsumerGroup $consumerGroup = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $consumerGroup = Data::mapOrNull($data, 'consumer_group');

        return new self(
            consumerGroup: $consumerGroup === null ? null : ConsumerGroup::fromArray($consumerGroup),
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
        ]);
    }
}
