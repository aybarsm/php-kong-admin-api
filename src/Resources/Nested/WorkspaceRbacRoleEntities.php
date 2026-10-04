<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources\Nested;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\RbacRoleEntity;
use Aybarsm\Kong\AdminApi\Models\RbacRoleEntityInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;
use Generator;

/**
 * RBAC role entities nested under one RBAC role (workspace-only paths): `/{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/entities` and `/{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/entities/{RBACRoleEntityId}`.
 *
 * Obtain it with `$client->workspaceRbacRoles()->entities($roleId)`.
 */
final readonly class WorkspaceRbacRoleEntities extends AbstractResource
{
    private const string SEGMENT = 'entities';

    /**
     * List one page of RBAC role entities (operationId `list-rbac_role_entitie-in-workspace`).
     *
     * @return Page<RbacRoleEntity>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/entities', 'list-rbac_role_entitie-in-workspace', OperationScope::WorkspaceOnly)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::WorkspaceOnly, self::SEGMENT), RbacRoleEntity::fromArray(...), $options);
    }

    /**
     * Lazily iterate every RBAC role entity across all pages (operationId `list-rbac_role_entitie-in-workspace`).
     *
     * @return Generator<int, RbacRoleEntity>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/entities', 'list-rbac_role_entitie-in-workspace', OperationScope::WorkspaceOnly)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::WorkspaceOnly, self::SEGMENT), RbacRoleEntity::fromArray(...), $options);
    }

    /**
     * Get an RBAC role entity by ID (operationId `get-rbac_role_entitie-in-workspace`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/entities/{RBACRoleEntityId}', 'get-rbac_role_entitie-in-workspace', OperationScope::WorkspaceOnly)]
    public function get(string $id): RbacRoleEntity
    {
        return RbacRoleEntity::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::WorkspaceOnly, self::SEGMENT, $id),
        ));
    }

    /**
     * Create an RBAC role entity (operationId `create-rbac_role_entitie-in-workspace`, body `RBACRoleEntity`).
     *
     * @param RbacRoleEntityInput|array<string, mixed> $rbacRoleEntity
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/entities', 'create-rbac_role_entitie-in-workspace', OperationScope::WorkspaceOnly)]
    public function create(RbacRoleEntityInput|array $rbacRoleEntity): RbacRoleEntity
    {
        return RbacRoleEntity::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::WorkspaceOnly, self::SEGMENT),
            body: $rbacRoleEntity,
        ));
    }

    /**
     * Update fields of an RBAC role entity (operationId `update-rbac_role_entitie-in-workspace`, PATCH, body `RBACRoleEntity`).
     *
     * @param RbacRoleEntityInput|array<string, mixed> $rbacRoleEntity only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/entities/{RBACRoleEntityId}', 'update-rbac_role_entitie-in-workspace', OperationScope::WorkspaceOnly)]
    public function update(string $id, RbacRoleEntityInput|array $rbacRoleEntity): RbacRoleEntity
    {
        return RbacRoleEntity::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::WorkspaceOnly, self::SEGMENT, $id),
            body: $rbacRoleEntity,
        ));
    }

    /**
     * Create or replace an RBAC role entity by ID (operationId `upsert-rbac_role_entitie-in-workspace`, PUT, body `RBACRoleEntity`).
     *
     * @param RbacRoleEntityInput|array<string, mixed> $rbacRoleEntity
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/entities/{RBACRoleEntityId}', 'upsert-rbac_role_entitie-in-workspace', OperationScope::WorkspaceOnly)]
    public function upsert(string $id, RbacRoleEntityInput|array $rbacRoleEntity): RbacRoleEntity
    {
        return RbacRoleEntity::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::WorkspaceOnly, self::SEGMENT, $id),
            body: $rbacRoleEntity,
        ));
    }

    /**
     * Delete an RBAC role entity (operationId `delete-rbac_role_entitie-in-workspace`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/entities/{RBACRoleEntityId}', 'delete-rbac_role_entitie-in-workspace', OperationScope::WorkspaceOnly)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::WorkspaceOnly, self::SEGMENT, $id));
    }
}
