<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\RbacRole;
use Aybarsm\Kong\AdminApi\Models\RbacRoleInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\Nested\WorkspaceRbacRoleEndpoints;
use Aybarsm\Kong\AdminApi\Resources\Nested\WorkspaceRbacRoleEntities;
use Generator;

/**
 * RBAC roles of the current workspace (spec tag "RBACRoles"): `/{workspace}/rbac/roles` and `/{workspace}/rbac/roles/{RBACRoleId}`.
 *
 * These paths exist only under `/{workspace}`; without a workspace the spec default `default` is used.
 */
final readonly class WorkspaceRbacRoles extends AbstractResource
{
    /**
     * List one page of RBAC roles (operationId `list-rbac_role-in-workspace`).
     *
     * @return Page<RbacRole>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/{workspace}/rbac/roles', 'list-rbac_role-in-workspace', OperationScope::WorkspaceOnly)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::WorkspaceOnly, 'rbac', 'roles'), RbacRole::fromArray(...), $options);
    }

    /**
     * Lazily iterate every RBAC role across all pages (operationId `list-rbac_role-in-workspace`).
     *
     * @return Generator<int, RbacRole>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/{workspace}/rbac/roles', 'list-rbac_role-in-workspace', OperationScope::WorkspaceOnly)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::WorkspaceOnly, 'rbac', 'roles'), RbacRole::fromArray(...), $options);
    }

    /**
     * The page after $page, or null when $page is the last (operationId `list-rbac_role-in-workspace`).
     *
     * Repeats list() with the page's `offset`, keeping the same filters and workspace. Kong's own `next`
     * link is not followed (it drops filters on nested lists; spec-notes Q6).
     *
     * @param Page<RbacRole> $page
     *
     * @return Page<RbacRole>|null
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/{workspace}/rbac/roles', 'list-rbac_role-in-workspace', OperationScope::WorkspaceOnly)]
    public function nextPage(Page $page, ?ListOptions $options = null): ?Page
    {
        return $page->offset === null ? null : $this->list(($options ?? new ListOptions())->withOffset($page->offset));
    }

    /**
     * Get an RBAC role by ID (operationId `get-rbac_role-in-workspace`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/{workspace}/rbac/roles/{RBACRoleId}', 'get-rbac_role-in-workspace', OperationScope::WorkspaceOnly)]
    public function get(string $id): RbacRole
    {
        return RbacRole::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::WorkspaceOnly, 'rbac', 'roles', $id),
        ));
    }

    /**
     * Create an RBAC role (operationId `create-rbac_role-in-workspace`, body `RBACRole`).
     *
     * @param RbacRoleInput|array<string, mixed> $rbacRole
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/{workspace}/rbac/roles', 'create-rbac_role-in-workspace', OperationScope::WorkspaceOnly)]
    public function create(RbacRoleInput|array $rbacRole): RbacRole
    {
        return RbacRole::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::WorkspaceOnly, 'rbac', 'roles'),
            body: $rbacRole,
        ));
    }

    /**
     * Update fields of an RBAC role (operationId `update-rbac_role-in-workspace`, PATCH, body `RBACRole`).
     *
     * @param RbacRoleInput|array<string, mixed> $rbacRole only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/{workspace}/rbac/roles/{RBACRoleId}', 'update-rbac_role-in-workspace', OperationScope::WorkspaceOnly)]
    public function update(string $id, RbacRoleInput|array $rbacRole): RbacRole
    {
        return RbacRole::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::WorkspaceOnly, 'rbac', 'roles', $id),
            body: $rbacRole,
        ));
    }

    /**
     * Create or replace an RBAC role by ID (operationId `upsert-rbac_role-in-workspace`, PUT, body `RBACRole`).
     *
     * @param RbacRoleInput|array<string, mixed> $rbacRole
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/{workspace}/rbac/roles/{RBACRoleId}', 'upsert-rbac_role-in-workspace', OperationScope::WorkspaceOnly)]
    public function upsert(string $id, RbacRoleInput|array $rbacRole): RbacRole
    {
        return RbacRole::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::WorkspaceOnly, 'rbac', 'roles', $id),
            body: $rbacRole,
        ));
    }

    /**
     * Delete an RBAC role (operationId `delete-rbac_role-in-workspace`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/{workspace}/rbac/roles/{RBACRoleId}', 'delete-rbac_role-in-workspace', OperationScope::WorkspaceOnly)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::WorkspaceOnly, 'rbac', 'roles', $id));
    }

    /**
     * Entity permissions of one RBAC role: `/{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/entities`.
     *
     * @throws InvalidArgumentException when $roleId is empty
     */
    public function entities(string $roleId): WorkspaceRbacRoleEntities
    {
        if ($roleId === '') {
            throw new InvalidArgumentException('RBAC role ID must not be empty.');
        }

        return new WorkspaceRbacRoleEntities($this->transport, [...$this->parent, 'rbac', 'roles', $roleId]);
    }

    /**
     * Endpoint permissions of one RBAC role: `/{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/endpoints`.
     *
     * @throws InvalidArgumentException when $roleId is empty
     */
    public function endpoints(string $roleId): WorkspaceRbacRoleEndpoints
    {
        if ($roleId === '') {
            throw new InvalidArgumentException('RBAC role ID must not be empty.');
        }

        return new WorkspaceRbacRoleEndpoints($this->transport, [...$this->parent, 'rbac', 'roles', $roleId]);
    }
}
