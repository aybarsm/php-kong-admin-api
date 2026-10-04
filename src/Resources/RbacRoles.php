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
use Generator;

/**
 * RBAC roles (spec tag "RBACRoles"): `/rbac_roles` and `/rbac_roles/{RBACRoleId}`.
 */
final readonly class RbacRoles extends AbstractResource
{
    private const string SEGMENT = 'rbac_roles';

    /**
     * List one page of RBAC roles (operationId `list-rbac_role`).
     *
     * @return Page<RbacRole>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/rbac_roles', 'list-rbac_role', OperationScope::GlobalOnly)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::GlobalOnly, self::SEGMENT), RbacRole::fromArray(...), $options);
    }

    /**
     * Lazily iterate every RBAC role across all pages (operationId `list-rbac_role`).
     *
     * @return Generator<int, RbacRole>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/rbac_roles', 'list-rbac_role', OperationScope::GlobalOnly)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::GlobalOnly, self::SEGMENT), RbacRole::fromArray(...), $options);
    }

    /**
     * Get an RBAC role by ID (operationId `get-rbac_role`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/rbac_roles/{RBACRoleId}', 'get-rbac_role', OperationScope::GlobalOnly)]
    public function get(string $id): RbacRole
    {
        return RbacRole::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id),
        ));
    }

    /**
     * Create an RBAC role (operationId `create-rbac_role`, body `RBACRole`).
     *
     * @param RbacRoleInput|array<string, mixed> $rbacRole
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/rbac_roles', 'create-rbac_role', OperationScope::GlobalOnly)]
    public function create(RbacRoleInput|array $rbacRole): RbacRole
    {
        return RbacRole::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT),
            body: $rbacRole,
        ));
    }

    /**
     * Update fields of an RBAC role (operationId `update-rbac_role`, PATCH, body `RBACRole`).
     *
     * @param RbacRoleInput|array<string, mixed> $rbacRole only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/rbac_roles/{RBACRoleId}', 'update-rbac_role', OperationScope::GlobalOnly)]
    public function update(string $id, RbacRoleInput|array $rbacRole): RbacRole
    {
        return RbacRole::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id),
            body: $rbacRole,
        ));
    }

    /**
     * Create or replace an RBAC role by ID (operationId `upsert-rbac_role`, PUT, body `RBACRole`).
     *
     * @param RbacRoleInput|array<string, mixed> $rbacRole
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/rbac_roles/{RBACRoleId}', 'upsert-rbac_role', OperationScope::GlobalOnly)]
    public function upsert(string $id, RbacRoleInput|array $rbacRole): RbacRole
    {
        return RbacRole::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id),
            body: $rbacRole,
        ));
    }

    /**
     * Delete an RBAC role (operationId `delete-rbac_role`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/rbac_roles/{RBACRoleId}', 'delete-rbac_role', OperationScope::GlobalOnly)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id));
    }
}
