<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources\Nested;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\RbacUserGroup;
use Aybarsm\Kong\AdminApi\Models\RbacUserGroupInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;
use Generator;

/**
 * RBAC user groups nested under one RBAC user (workspace-only paths): `/{workspace}/rbac/users/{RBACUserIdForNestedEntities}/groups` and `/{workspace}/rbac/users/{RBACUserIdForNestedEntities}/groups/{RBACUserGroupId}`.
 *
 * Obtain it with `$client->workspaceRbacUsers()->groups($userId)`.
 */
final readonly class WorkspaceRbacUserGroups extends AbstractResource
{
    private const string SEGMENT = 'groups';

    /**
     * List one page of RBAC user groups (operationId `list-rbac_user_group-in-workspace`).
     *
     * @return Page<RbacUserGroup>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/{workspace}/rbac/users/{RBACUserIdForNestedEntities}/groups', 'list-rbac_user_group-in-workspace', OperationScope::WorkspaceOnly)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::WorkspaceOnly, self::SEGMENT), RbacUserGroup::fromArray(...), $options);
    }

    /**
     * Lazily iterate every RBAC user group across all pages (operationId `list-rbac_user_group-in-workspace`).
     *
     * @return Generator<int, RbacUserGroup>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/{workspace}/rbac/users/{RBACUserIdForNestedEntities}/groups', 'list-rbac_user_group-in-workspace', OperationScope::WorkspaceOnly)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::WorkspaceOnly, self::SEGMENT), RbacUserGroup::fromArray(...), $options);
    }

    /**
     * Get an RBAC user group by ID (operationId `get-rbac_user_group-in-workspace`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/{workspace}/rbac/users/{RBACUserIdForNestedEntities}/groups/{RBACUserGroupId}', 'get-rbac_user_group-in-workspace', OperationScope::WorkspaceOnly)]
    public function get(string $id): RbacUserGroup
    {
        return RbacUserGroup::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::WorkspaceOnly, self::SEGMENT, $id),
        ));
    }

    /**
     * Create an RBAC user group (operationId `create-rbac_user_group-in-workspace`, body `RBACUserGroup`).
     *
     * @param RbacUserGroupInput|array<string, mixed> $rbacUserGroup
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/{workspace}/rbac/users/{RBACUserIdForNestedEntities}/groups', 'create-rbac_user_group-in-workspace', OperationScope::WorkspaceOnly)]
    public function create(RbacUserGroupInput|array $rbacUserGroup): RbacUserGroup
    {
        return RbacUserGroup::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::WorkspaceOnly, self::SEGMENT),
            body: $rbacUserGroup,
        ));
    }

    /**
     * Update fields of an RBAC user group (operationId `update-rbac_user_group-in-workspace`, PATCH, body `RBACUserGroup`).
     *
     * @param RbacUserGroupInput|array<string, mixed> $rbacUserGroup only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/{workspace}/rbac/users/{RBACUserIdForNestedEntities}/groups/{RBACUserGroupId}', 'update-rbac_user_group-in-workspace', OperationScope::WorkspaceOnly)]
    public function update(string $id, RbacUserGroupInput|array $rbacUserGroup): RbacUserGroup
    {
        return RbacUserGroup::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::WorkspaceOnly, self::SEGMENT, $id),
            body: $rbacUserGroup,
        ));
    }

    /**
     * Create or replace an RBAC user group by ID (operationId `upsert-rbac_user_group-in-workspace`, PUT, body `RBACUserGroup`).
     *
     * @param RbacUserGroupInput|array<string, mixed> $rbacUserGroup
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/{workspace}/rbac/users/{RBACUserIdForNestedEntities}/groups/{RBACUserGroupId}', 'upsert-rbac_user_group-in-workspace', OperationScope::WorkspaceOnly)]
    public function upsert(string $id, RbacUserGroupInput|array $rbacUserGroup): RbacUserGroup
    {
        return RbacUserGroup::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::WorkspaceOnly, self::SEGMENT, $id),
            body: $rbacUserGroup,
        ));
    }

    /**
     * Delete an RBAC user group (operationId `delete-rbac_user_group-in-workspace`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/{workspace}/rbac/users/{RBACUserIdForNestedEntities}/groups/{RBACUserGroupId}', 'delete-rbac_user_group-in-workspace', OperationScope::WorkspaceOnly)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::WorkspaceOnly, self::SEGMENT, $id));
    }
}
