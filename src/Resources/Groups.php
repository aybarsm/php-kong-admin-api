<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\Group;
use Aybarsm\Kong\AdminApi\Models\GroupInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\Nested\GroupRoles;
use Generator;

/**
 * Groups (spec tag "Groups"): `/groups` and `/groups/{GroupId}`.
 */
final readonly class Groups extends AbstractResource
{
    private const string SEGMENT = 'groups';

    /**
     * List one page of Groups (operationId `list-group`).
     *
     * @return Page<Group>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/groups', 'list-group', OperationScope::GlobalOnly)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::GlobalOnly, self::SEGMENT), Group::fromArray(...), $options);
    }

    /**
     * Lazily iterate every Group across all pages (operationId `list-group`).
     *
     * @return Generator<int, Group>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/groups', 'list-group', OperationScope::GlobalOnly)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::GlobalOnly, self::SEGMENT), Group::fromArray(...), $options);
    }

    /**
     * Get a Group by ID (operationId `get-group`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/groups/{GroupId}', 'get-group', OperationScope::GlobalOnly)]
    public function get(string $id): Group
    {
        return Group::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id),
        ));
    }

    /**
     * Create a Group (operationId `create-group`, body `Group`).
     *
     * @param GroupInput|array<string, mixed> $group
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/groups', 'create-group', OperationScope::GlobalOnly)]
    public function create(GroupInput|array $group): Group
    {
        return Group::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT),
            body: $group,
        ));
    }

    /**
     * Update fields of a Group (operationId `update-group`, PATCH, body `Group`).
     *
     * @param GroupInput|array<string, mixed> $group only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/groups/{GroupId}', 'update-group', OperationScope::GlobalOnly)]
    public function update(string $id, GroupInput|array $group): Group
    {
        return Group::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id),
            body: $group,
        ));
    }

    /**
     * Create or replace a Group by ID (operationId `upsert-group`, PUT, body `Group`).
     *
     * @param GroupInput|array<string, mixed> $group
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/groups/{GroupId}', 'upsert-group', OperationScope::GlobalOnly)]
    public function upsert(string $id, GroupInput|array $group): Group
    {
        return Group::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id),
            body: $group,
        ));
    }

    /**
     * Delete a Group (operationId `delete-group`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/groups/{GroupId}', 'delete-group', OperationScope::GlobalOnly)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id));
    }

    /**
     * RBAC roles of one Group: `/groups/{GroupId}/roles`.
     *
     * @throws InvalidArgumentException when $groupId is empty
     */
    public function roles(string $groupId): GroupRoles
    {
        if ($groupId === '') {
            throw new InvalidArgumentException('Group ID must not be empty.');
        }

        return new GroupRoles($this->transport, [...$this->parent, self::SEGMENT, $groupId]);
    }
}
