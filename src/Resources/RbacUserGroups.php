<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\RbacUserGroup;
use Aybarsm\Kong\AdminApi\Models\RbacUserGroupInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Generator;

/**
 * RBAC user groups (spec tag "RBACUserGroups"): `/rbac_user_groups` and `/rbac_user_groups/{RBACUserGroupId}`.
 */
final readonly class RbacUserGroups extends AbstractResource
{
    private const string SEGMENT = 'rbac_user_groups';

    /**
     * List one page of RBAC user groups (operationId `list-rbac_user_group`).
     *
     * @return Page<RbacUserGroup>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/rbac_user_groups', 'list-rbac_user_group', OperationScope::GlobalOnly)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::GlobalOnly, self::SEGMENT), RbacUserGroup::fromArray(...), $options);
    }

    /**
     * Lazily iterate every RBAC user group across all pages (operationId `list-rbac_user_group`).
     *
     * @return Generator<int, RbacUserGroup>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/rbac_user_groups', 'list-rbac_user_group', OperationScope::GlobalOnly)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::GlobalOnly, self::SEGMENT), RbacUserGroup::fromArray(...), $options);
    }

    /**
     * Get an RBAC user group by ID (operationId `get-rbac_user_group`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/rbac_user_groups/{RBACUserGroupId}', 'get-rbac_user_group', OperationScope::GlobalOnly)]
    public function get(string $id): RbacUserGroup
    {
        return RbacUserGroup::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id),
        ));
    }

    /**
     * Create an RBAC user group (operationId `create-rbac_user_group`, body `RBACUserGroup`).
     *
     * @param RbacUserGroupInput|array<string, mixed> $rbacUserGroup
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/rbac_user_groups', 'create-rbac_user_group', OperationScope::GlobalOnly)]
    public function create(RbacUserGroupInput|array $rbacUserGroup): RbacUserGroup
    {
        return RbacUserGroup::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT),
            body: $rbacUserGroup,
        ));
    }

    /**
     * Update fields of an RBAC user group (operationId `update-rbac_user_group`, PATCH, body `RBACUserGroup`).
     *
     * @param RbacUserGroupInput|array<string, mixed> $rbacUserGroup only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/rbac_user_groups/{RBACUserGroupId}', 'update-rbac_user_group', OperationScope::GlobalOnly)]
    public function update(string $id, RbacUserGroupInput|array $rbacUserGroup): RbacUserGroup
    {
        return RbacUserGroup::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id),
            body: $rbacUserGroup,
        ));
    }

    /**
     * Create or replace an RBAC user group by ID (operationId `upsert-rbac_user_group`, PUT, body `RBACUserGroup`).
     *
     * @param RbacUserGroupInput|array<string, mixed> $rbacUserGroup
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/rbac_user_groups/{RBACUserGroupId}', 'upsert-rbac_user_group', OperationScope::GlobalOnly)]
    public function upsert(string $id, RbacUserGroupInput|array $rbacUserGroup): RbacUserGroup
    {
        return RbacUserGroup::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id),
            body: $rbacUserGroup,
        ));
    }

    /**
     * Delete an RBAC user group (operationId `delete-rbac_user_group`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/rbac_user_groups/{RBACUserGroupId}', 'delete-rbac_user_group', OperationScope::GlobalOnly)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id));
    }
}
