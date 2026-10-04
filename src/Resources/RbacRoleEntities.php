<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\RbacRoleEntity;
use Aybarsm\Kong\AdminApi\Models\RbacRoleEntityInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Generator;

/**
 * RBAC role entities (spec tag "RBACRoleEntities"): `/rbac_role_entities` and `/rbac_role_entities/{RBACRoleEntityId}`.
 */
final readonly class RbacRoleEntities extends AbstractResource
{
    private const string SEGMENT = 'rbac_role_entities';

    /**
     * List one page of RBAC role entities (operationId `list-rbac_role_entitie`).
     *
     * @return Page<RbacRoleEntity>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/rbac_role_entities', 'list-rbac_role_entitie', OperationScope::GlobalOnly)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::GlobalOnly, self::SEGMENT), RbacRoleEntity::fromArray(...), $options);
    }

    /**
     * Lazily iterate every RBAC role entity across all pages (operationId `list-rbac_role_entitie`).
     *
     * @return Generator<int, RbacRoleEntity>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/rbac_role_entities', 'list-rbac_role_entitie', OperationScope::GlobalOnly)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::GlobalOnly, self::SEGMENT), RbacRoleEntity::fromArray(...), $options);
    }

    /**
     * The page after $page, or null when $page is the last (operationId `list-rbac_role_entitie`).
     *
     * Repeats list() with the page's `offset`, keeping the same filters and workspace. Kong's own `next`
     * link is not followed (it drops filters on nested lists; spec-notes Q6).
     *
     * @param Page<RbacRoleEntity> $page
     *
     * @return Page<RbacRoleEntity>|null
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/rbac_role_entities', 'list-rbac_role_entitie', OperationScope::GlobalOnly)]
    public function nextPage(Page $page, ?ListOptions $options = null): ?Page
    {
        return $page->offset === null ? null : $this->list(($options ?? new ListOptions())->withOffset($page->offset));
    }

    /**
     * Get an RBAC role entity by ID (operationId `get-rbac_role_entitie`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/rbac_role_entities/{RBACRoleEntityId}', 'get-rbac_role_entitie', OperationScope::GlobalOnly)]
    public function get(string $id): RbacRoleEntity
    {
        return RbacRoleEntity::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id),
        ));
    }

    /**
     * Create an RBAC role entity (operationId `create-rbac_role_entitie`, body `RBACRoleEntity`).
     *
     * @param RbacRoleEntityInput|array<string, mixed> $rbacRoleEntity
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/rbac_role_entities', 'create-rbac_role_entitie', OperationScope::GlobalOnly)]
    public function create(RbacRoleEntityInput|array $rbacRoleEntity): RbacRoleEntity
    {
        return RbacRoleEntity::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT),
            body: $rbacRoleEntity,
        ));
    }

    /**
     * Update fields of an RBAC role entity (operationId `update-rbac_role_entitie`, PATCH, body `RBACRoleEntity`).
     *
     * @param RbacRoleEntityInput|array<string, mixed> $rbacRoleEntity only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/rbac_role_entities/{RBACRoleEntityId}', 'update-rbac_role_entitie', OperationScope::GlobalOnly)]
    public function update(string $id, RbacRoleEntityInput|array $rbacRoleEntity): RbacRoleEntity
    {
        return RbacRoleEntity::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id),
            body: $rbacRoleEntity,
        ));
    }

    /**
     * Create or replace an RBAC role entity by ID (operationId `upsert-rbac_role_entitie`, PUT, body `RBACRoleEntity`).
     *
     * @param RbacRoleEntityInput|array<string, mixed> $rbacRoleEntity
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/rbac_role_entities/{RBACRoleEntityId}', 'upsert-rbac_role_entitie', OperationScope::GlobalOnly)]
    public function upsert(string $id, RbacRoleEntityInput|array $rbacRoleEntity): RbacRoleEntity
    {
        return RbacRoleEntity::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id),
            body: $rbacRoleEntity,
        ));
    }

    /**
     * Delete an RBAC role entity (operationId `delete-rbac_role_entitie`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/rbac_role_entities/{RBACRoleEntityId}', 'delete-rbac_role_entitie', OperationScope::GlobalOnly)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id));
    }
}
