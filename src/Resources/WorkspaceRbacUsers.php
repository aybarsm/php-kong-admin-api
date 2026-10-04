<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\RbacUser;
use Aybarsm\Kong\AdminApi\Models\RbacUserInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\Nested\WorkspaceRbacUserGroups;
use Aybarsm\Kong\AdminApi\Resources\Nested\WorkspaceRbacUserRoles;
use Generator;

/**
 * RBAC users of the current workspace (spec tag "RBACUsers"): `/{workspace}/rbac/users` and `/{workspace}/rbac/users/{RBACUserId}`.
 *
 * These paths exist only under `/{workspace}`; without a workspace the spec default `default` is used.
 */
final readonly class WorkspaceRbacUsers extends AbstractResource
{
    /**
     * List one page of RBAC users (operationId `list-rbac_user-in-workspace`).
     *
     * @return Page<RbacUser>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/{workspace}/rbac/users', 'list-rbac_user-in-workspace', OperationScope::WorkspaceOnly)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::WorkspaceOnly, 'rbac', 'users'), RbacUser::fromArray(...), $options);
    }

    /**
     * Lazily iterate every RBAC user across all pages (operationId `list-rbac_user-in-workspace`).
     *
     * @return Generator<int, RbacUser>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/{workspace}/rbac/users', 'list-rbac_user-in-workspace', OperationScope::WorkspaceOnly)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::WorkspaceOnly, 'rbac', 'users'), RbacUser::fromArray(...), $options);
    }

    /**
     * The page after $page, or null when $page is the last (operationId `list-rbac_user-in-workspace`).
     *
     * Repeats list() with the page's `offset`, keeping the same filters and workspace. Kong's own `next`
     * link is not followed (it drops filters on nested lists; spec-notes Q6).
     *
     * @param Page<RbacUser> $page
     *
     * @return Page<RbacUser>|null
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/{workspace}/rbac/users', 'list-rbac_user-in-workspace', OperationScope::WorkspaceOnly)]
    public function nextPage(Page $page, ?ListOptions $options = null): ?Page
    {
        return $page->offset === null ? null : $this->list(($options ?? new ListOptions())->withOffset($page->offset));
    }

    /**
     * Get an RBAC user by ID (operationId `get-rbac_user-in-workspace`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/{workspace}/rbac/users/{RBACUserId}', 'get-rbac_user-in-workspace', OperationScope::WorkspaceOnly)]
    public function get(string $id): RbacUser
    {
        return RbacUser::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::WorkspaceOnly, 'rbac', 'users', $id),
        ));
    }

    /**
     * Create an RBAC user (operationId `create-rbac_user-in-workspace`, body `RBACUser`).
     *
     * @param RbacUserInput|array<string, mixed> $rbacUser
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/{workspace}/rbac/users', 'create-rbac_user-in-workspace', OperationScope::WorkspaceOnly)]
    public function create(RbacUserInput|array $rbacUser): RbacUser
    {
        return RbacUser::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::WorkspaceOnly, 'rbac', 'users'),
            body: $rbacUser,
        ));
    }

    /**
     * Update fields of an RBAC user (operationId `update-rbac_user-in-workspace`, PATCH, body `RBACUser`).
     *
     * @param RbacUserInput|array<string, mixed> $rbacUser only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/{workspace}/rbac/users/{RBACUserId}', 'update-rbac_user-in-workspace', OperationScope::WorkspaceOnly)]
    public function update(string $id, RbacUserInput|array $rbacUser): RbacUser
    {
        return RbacUser::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::WorkspaceOnly, 'rbac', 'users', $id),
            body: $rbacUser,
        ));
    }

    /**
     * Create or replace an RBAC user by ID (operationId `upsert-rbac_user-in-workspace`, PUT, body `RBACUser`).
     *
     * @param RbacUserInput|array<string, mixed> $rbacUser
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/{workspace}/rbac/users/{RBACUserId}', 'upsert-rbac_user-in-workspace', OperationScope::WorkspaceOnly)]
    public function upsert(string $id, RbacUserInput|array $rbacUser): RbacUser
    {
        return RbacUser::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::WorkspaceOnly, 'rbac', 'users', $id),
            body: $rbacUser,
        ));
    }

    /**
     * Delete an RBAC user (operationId `delete-rbac_user-in-workspace`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/{workspace}/rbac/users/{RBACUserId}', 'delete-rbac_user-in-workspace', OperationScope::WorkspaceOnly)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::WorkspaceOnly, 'rbac', 'users', $id));
    }

    /**
     * Groups of one RBAC user: `/{workspace}/rbac/users/{RBACUserIdForNestedEntities}/groups`.
     *
     * @throws InvalidArgumentException when $userId is empty
     */
    public function groups(string $userId): WorkspaceRbacUserGroups
    {
        if ($userId === '') {
            throw new InvalidArgumentException('RBAC user ID must not be empty.');
        }

        return new WorkspaceRbacUserGroups($this->transport, [...$this->parent, 'rbac', 'users', $userId]);
    }

    /**
     * Roles of one RBAC user: `/{workspace}/rbac/users/{RBACUserIdForNestedEntities}/roles`.
     *
     * @throws InvalidArgumentException when $userId is empty
     */
    public function roles(string $userId): WorkspaceRbacUserRoles
    {
        if ($userId === '') {
            throw new InvalidArgumentException('RBAC user ID must not be empty.');
        }

        return new WorkspaceRbacUserRoles($this->transport, [...$this->parent, 'rbac', 'users', $userId]);
    }
}
