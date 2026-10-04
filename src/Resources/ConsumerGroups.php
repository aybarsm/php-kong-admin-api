<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\ConsumerGroup;
use Aybarsm\Kong\AdminApi\Models\ConsumerGroupInput;
use Aybarsm\Kong\AdminApi\Models\ConsumerGroupInsideWrapper;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\Nested\ConsumerGroupConsumers;
use Aybarsm\Kong\AdminApi\Resources\Nested\ConsumerGroupPlugins;
use Aybarsm\Kong\AdminApi\Resources\Nested\ConsumerGroupRateLimitingAdvancedOverride;
use Generator;

/**
 * Consumer Groups (spec tag "Consumer Groups"): `/consumer_groups` and `/consumer_groups/{ConsumerGroupId}`.
 */
final readonly class ConsumerGroups extends AbstractResource
{
    private const string SEGMENT = 'consumer_groups';

    /**
     * List one page of Consumer Groups (operationId `list-consumer_group`).
     *
     * @return Page<ConsumerGroup>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/consumer_groups', 'list-consumer_group', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), ConsumerGroup::fromArray(...), $options);
    }

    /**
     * Lazily iterate every Consumer Group across all pages (operationId `list-consumer_group`).
     *
     * @return Generator<int, ConsumerGroup>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/consumer_groups', 'list-consumer_group', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), ConsumerGroup::fromArray(...), $options);
    }

    /**
     * The page after $page, or null when $page is the last (operationId `list-consumer_group`).
     *
     * Repeats list() with the page's `offset`, keeping the same filters and workspace. Kong's own `next`
     * link is not followed (it drops filters on nested lists; spec-notes Q6).
     *
     * @param Page<ConsumerGroup> $page
     *
     * @return Page<ConsumerGroup>|null
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/consumer_groups', 'list-consumer_group', OperationScope::Both)]
    public function nextPage(Page $page, ?ListOptions $options = null): ?Page
    {
        return $page->offset === null ? null : $this->list(($options ?? new ListOptions())->withOffset($page->offset));
    }

    /**
     * Get a Consumer Group by ID (operationId `get-consumer_group`).
     *
     * Returns the spec wrapper `{consumer_group}` (spec-notes Q9).
     *
     * @param bool|null $listConsumers expand the group with its consumers (spec parameter `ListConsumers`)
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/consumer_groups/{ConsumerGroupId}', 'get-consumer_group', OperationScope::Both)]
    public function get(string $id, ?bool $listConsumers = null): ConsumerGroupInsideWrapper
    {
        return ConsumerGroupInsideWrapper::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            ['list_consumers' => $listConsumers],
        ));
    }

    /**
     * Create a Consumer Group (operationId `create-consumer_group`, body `ConsumerGroup`).
     *
     * @param ConsumerGroupInput|array<string, mixed> $consumerGroup
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/consumer_groups', 'create-consumer_group', OperationScope::Both)]
    public function create(ConsumerGroupInput|array $consumerGroup): ConsumerGroup
    {
        return ConsumerGroup::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $consumerGroup,
        ));
    }

    /**
     * Update fields of a Consumer Group (operationId `update-consumer_group`, PATCH, body `ConsumerGroup`).
     *
     * @param ConsumerGroupInput|array<string, mixed> $consumerGroup only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/consumer_groups/{ConsumerGroupId}', 'update-consumer_group', OperationScope::Both)]
    public function update(string $id, ConsumerGroupInput|array $consumerGroup): ConsumerGroup
    {
        return ConsumerGroup::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $consumerGroup,
        ));
    }

    /**
     * Create or replace a Consumer Group by ID (operationId `upsert-consumer_group`, PUT, body `ConsumerGroup`).
     *
     * @param ConsumerGroupInput|array<string, mixed> $consumerGroup
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/consumer_groups/{ConsumerGroupId}', 'upsert-consumer_group', OperationScope::Both)]
    public function upsert(string $id, ConsumerGroupInput|array $consumerGroup): ConsumerGroup
    {
        return ConsumerGroup::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $consumerGroup,
        ));
    }

    /**
     * Delete a Consumer Group (operationId `delete-consumer_group`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/consumer_groups/{ConsumerGroupId}', 'delete-consumer_group', OperationScope::Both)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $id));
    }

    /**
     * Consumers in one Consumer Group: `/consumer_groups/{ConsumerGroupId}/consumers`.
     *
     * @throws InvalidArgumentException when $groupIdOrName is empty
     */
    public function consumers(string $groupIdOrName): ConsumerGroupConsumers
    {
        if ($groupIdOrName === '') {
            throw new InvalidArgumentException('Consumer Group ID or name must not be empty.');
        }

        return new ConsumerGroupConsumers($this->transport, [...$this->parent, self::SEGMENT, $groupIdOrName]);
    }

    /**
     * Plugins scoped to one Consumer Group: `/consumer_groups/{ConsumerGroupId}/plugins`.
     *
     * @throws InvalidArgumentException when $groupId is empty
     */
    public function plugins(string $groupId): ConsumerGroupPlugins
    {
        if ($groupId === '') {
            throw new InvalidArgumentException('Consumer Group ID must not be empty.');
        }

        return new ConsumerGroupPlugins($this->transport, [...$this->parent, self::SEGMENT, $groupId]);
    }

    /**
     * The rate-limiting-advanced override of one Consumer Group: `/consumer_groups/{ConsumerGroupId}/overrides/plugins/rate-limiting-advanced`.
     *
     * @throws InvalidArgumentException when $groupId is empty
     */
    public function rateLimitingAdvancedOverride(string $groupId): ConsumerGroupRateLimitingAdvancedOverride
    {
        if ($groupId === '') {
            throw new InvalidArgumentException('Consumer Group ID must not be empty.');
        }

        return new ConsumerGroupRateLimitingAdvancedOverride($this->transport, [...$this->parent, self::SEGMENT, $groupId]);
    }
}
