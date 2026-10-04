<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources\Nested;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\Consumer;
use Aybarsm\Kong\AdminApi\Models\ConsumerGroupMemberInput;
use Aybarsm\Kong\AdminApi\Models\ConsumerGroupMembership;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;
use Generator;

/**
 * Consumers in one Consumer Group: `/consumer_groups/{ConsumerGroupId}/consumers` and `/consumer_groups/{ConsumerGroupId}/consumers/{ConsumerIdOrUsername}`.
 *
 * Obtain it with `$client->consumerGroups()->consumers($groupIdOrName)`.
 */
final readonly class ConsumerGroupConsumers extends AbstractResource
{
    private const string SEGMENT = 'consumers';

    /**
     * List one page (operationId `list-consumers-for-consumer-group`).
     *
     * @return Page<Consumer>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/consumer_groups/{ConsumerGroupId}/consumers', 'list-consumers-for-consumer-group', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), Consumer::fromArray(...), $options);
    }

    /**
     * Lazily iterate every page (operationId `list-consumers-for-consumer-group`).
     *
     * @return Generator<int, Consumer>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/consumer_groups/{ConsumerGroupId}/consumers', 'list-consumers-for-consumer-group', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), Consumer::fromArray(...), $options);
    }

    /**
     * Add a membership (operationId `add-consumer-to-group`).
     *
     * @param ConsumerGroupMemberInput|array<string, mixed> $membership
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/consumer_groups/{ConsumerGroupId}/consumers', 'add-consumer-to-group', OperationScope::Both)]
    public function add(ConsumerGroupMemberInput|array $membership): ConsumerGroupMembership
    {
        return ConsumerGroupMembership::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $membership,
        ));
    }

    /**
     * Remove every membership (operationId `remove-all-consumers-from-consumer-group`).
     *
     * Marked `x-unstable` in the spec.
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_DELETE, '/consumer_groups/{ConsumerGroupId}/consumers', 'remove-all-consumers-from-consumer-group', OperationScope::Both)]
    public function removeAll(): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT));
    }

    /**
     * Remove one membership by Consumer ID or username (operationId `remove-consumer-from-group`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $consumerIdOrUsername is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/consumer_groups/{ConsumerGroupId}/consumers/{ConsumerIdOrUsername}', 'remove-consumer-from-group', OperationScope::Both)]
    public function remove(string $consumerIdOrUsername): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $consumerIdOrUsername));
    }
}
