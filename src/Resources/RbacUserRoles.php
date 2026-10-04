<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\RbacUserRole;
use Aybarsm\Kong\AdminApi\Models\RbacUserRoleInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Generator;

/**
 * RBAC user roles (spec tag "RBACUserRoles"): `/rbac_user_roles` and `/rbac_user_roles/{RBACUserRoleId}`.
 */
final readonly class RbacUserRoles extends AbstractResource
{
    private const string SEGMENT = 'rbac_user_roles';

    /**
     * List one page of RBAC user roles (operationId `list-rbac_user_role`).
     *
     * @return Page<RbacUserRole>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/rbac_user_roles', 'list-rbac_user_role', OperationScope::GlobalOnly)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::GlobalOnly, self::SEGMENT), RbacUserRole::fromArray(...), $options);
    }

    /**
     * Lazily iterate every RBAC user role across all pages (operationId `list-rbac_user_role`).
     *
     * @return Generator<int, RbacUserRole>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/rbac_user_roles', 'list-rbac_user_role', OperationScope::GlobalOnly)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::GlobalOnly, self::SEGMENT), RbacUserRole::fromArray(...), $options);
    }

    /**
     * Get an RBAC user role by ID (operationId `get-rbac_user_role`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/rbac_user_roles/{RBACUserRoleId}', 'get-rbac_user_role', OperationScope::Both)]
    public function get(string $id): RbacUserRole
    {
        return RbacUserRole::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
        ));
    }

    /**
     * Create an RBAC user role (operationId `create-rbac_user_role`, body `RBACUserRole`).
     *
     * @param RbacUserRoleInput|array<string, mixed> $rbacUserRole
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/rbac_user_roles', 'create-rbac_user_role', OperationScope::GlobalOnly)]
    public function create(RbacUserRoleInput|array $rbacUserRole): RbacUserRole
    {
        return RbacUserRole::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT),
            body: $rbacUserRole,
        ));
    }

    /**
     * Update fields of an RBAC user role (operationId `update-rbac_user_role`, PATCH, body `RBACUserRole`).
     *
     * @param RbacUserRoleInput|array<string, mixed> $rbacUserRole only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/rbac_user_roles/{RBACUserRoleId}', 'update-rbac_user_role', OperationScope::Both)]
    public function update(string $id, RbacUserRoleInput|array $rbacUserRole): RbacUserRole
    {
        return RbacUserRole::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $rbacUserRole,
        ));
    }

    /**
     * Create or replace an RBAC user role by ID (operationId `upsert-rbac_user_role`, PUT, body `RBACUserRole`).
     *
     * @param RbacUserRoleInput|array<string, mixed> $rbacUserRole
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/rbac_user_roles/{RBACUserRoleId}', 'upsert-rbac_user_role', OperationScope::Both)]
    public function upsert(string $id, RbacUserRoleInput|array $rbacUserRole): RbacUserRole
    {
        return RbacUserRole::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $rbacUserRole,
        ));
    }

    /**
     * Delete an RBAC user role (operationId `delete-rbac_user_role`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/rbac_user_roles/{RBACUserRoleId}', 'delete-rbac_user_role', OperationScope::GlobalOnly)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id));
    }
}
