<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources\Nested;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\ConsumerGroup;
use Aybarsm\Kong\AdminApi\Models\ConsumerGroupAssignmentInput;
use Aybarsm\Kong\AdminApi\Models\ConsumerMembership;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;
use Generator;

/**
 * Consumer Group memberships of one Consumer: `/consumers/{ConsumerIdOrUsername}/consumer_groups` and `/consumers/{ConsumerIdOrUsername}/consumer_groups/{ConsumerGroupId}`.
 *
 * Obtain it with `$client->consumers()->consumerGroups($idOrUsername)`.
 */
final readonly class ConsumerConsumerGroups extends AbstractResource
{
    private const string SEGMENT = 'consumer_groups';

    /**
     * List one page (operationId `list-consumer-groups-for-consumer`).
     *
     * @return Page<ConsumerGroup>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/consumers/{ConsumerIdOrUsername}/consumer_groups', 'list-consumer-groups-for-consumer', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), ConsumerGroup::fromArray(...), $options);
    }

    /**
     * Lazily iterate every page (operationId `list-consumer-groups-for-consumer`).
     *
     * @return Generator<int, ConsumerGroup>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/consumers/{ConsumerIdOrUsername}/consumer_groups', 'list-consumer-groups-for-consumer', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), ConsumerGroup::fromArray(...), $options);
    }

    /**
     * Add a membership (operationId `add-consumer-to-specific-consumer-group`).
     *
     * @param ConsumerGroupAssignmentInput|array<string, mixed> $membership
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/consumers/{ConsumerIdOrUsername}/consumer_groups', 'add-consumer-to-specific-consumer-group', OperationScope::Both)]
    public function add(ConsumerGroupAssignmentInput|array $membership): ConsumerMembership
    {
        return ConsumerMembership::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $membership,
        ));
    }

    /**
     * Remove every membership (operationId `remove-consumer-from-all-consumer-groups`).
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_DELETE, '/consumers/{ConsumerIdOrUsername}/consumer_groups', 'remove-consumer-from-all-consumer-groups', OperationScope::Both)]
    public function removeAll(): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT));
    }

    /**
     * Remove one membership by Consumer Group ID (operationId `remove-consumer-from-consumer-group`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $groupId is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/consumers/{ConsumerIdOrUsername}/consumer_groups/{ConsumerGroupId}', 'remove-consumer-from-consumer-group', OperationScope::Both)]
    public function remove(string $groupId): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $groupId));
    }
}
