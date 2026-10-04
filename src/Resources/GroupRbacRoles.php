<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\RbacGroupRole;
use Aybarsm\Kong\AdminApi\Models\RbacGroupRoleInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Generator;

/**
 * group RBAC roles (spec tag "RBACGroupRoles"): `/group_rbac_roles` and `/group_rbac_roles/{RBACGroupRoleId}`.
 */
final readonly class GroupRbacRoles extends AbstractResource
{
    private const string SEGMENT = 'group_rbac_roles';

    /**
     * List one page of group RBAC roles (operationId `list-group_rbac_role`).
     *
     * @return Page<RbacGroupRole>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/group_rbac_roles', 'list-group_rbac_role', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), RbacGroupRole::fromArray(...), $options);
    }

    /**
     * Lazily iterate every group RBAC role across all pages (operationId `list-group_rbac_role`).
     *
     * @return Generator<int, RbacGroupRole>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/group_rbac_roles', 'list-group_rbac_role', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), RbacGroupRole::fromArray(...), $options);
    }

    /**
     * The page after $page, or null when $page is the last (operationId `list-group_rbac_role`).
     *
     * Repeats list() with the page's `offset`, keeping the same filters and workspace. Kong's own `next`
     * link is not followed (it drops filters on nested lists; spec-notes Q6).
     *
     * @param Page<RbacGroupRole> $page
     *
     * @return Page<RbacGroupRole>|null
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/group_rbac_roles', 'list-group_rbac_role', OperationScope::Both)]
    public function nextPage(Page $page, ?ListOptions $options = null): ?Page
    {
        return $page->offset === null ? null : $this->list(($options ?? new ListOptions())->withOffset($page->offset));
    }

    /**
     * Get a group RBAC role by ID (operationId `get-group_rbac_role`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/group_rbac_roles/{RBACGroupRoleId}', 'get-group_rbac_role', OperationScope::Both)]
    public function get(string $id): RbacGroupRole
    {
        return RbacGroupRole::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
        ));
    }

    /**
     * Create a group RBAC role (operationId `create-group_rbac_role`, body `RBACGroupRole`).
     *
     * @param RbacGroupRoleInput|array<string, mixed> $rbacGroupRole
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/group_rbac_roles', 'create-group_rbac_role', OperationScope::Both)]
    public function create(RbacGroupRoleInput|array $rbacGroupRole): RbacGroupRole
    {
        return RbacGroupRole::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $rbacGroupRole,
        ));
    }

    /**
     * Update fields of a group RBAC role (operationId `update-group_rbac_role`, PATCH, body `RBACGroupRole`).
     *
     * @param RbacGroupRoleInput|array<string, mixed> $rbacGroupRole only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/group_rbac_roles/{RBACGroupRoleId}', 'update-group_rbac_role', OperationScope::Both)]
    public function update(string $id, RbacGroupRoleInput|array $rbacGroupRole): RbacGroupRole
    {
        return RbacGroupRole::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $rbacGroupRole,
        ));
    }

    /**
     * Create or replace a group RBAC role by ID (operationId `upsert-group_rbac_role`, PUT, body `RBACGroupRole`).
     *
     * @param RbacGroupRoleInput|array<string, mixed> $rbacGroupRole
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/group_rbac_roles/{RBACGroupRoleId}', 'upsert-group_rbac_role', OperationScope::Both)]
    public function upsert(string $id, RbacGroupRoleInput|array $rbacGroupRole): RbacGroupRole
    {
        return RbacGroupRole::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $rbacGroupRole,
        ));
    }

    /**
     * Delete a group RBAC role (operationId `delete-group_rbac_role`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/group_rbac_roles/{RBACGroupRoleId}', 'delete-group_rbac_role', OperationScope::Both)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $id));
    }
}
