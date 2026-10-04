<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\RbacRoleEndpoint;
use Aybarsm\Kong\AdminApi\Models\RbacRoleEndpointInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Generator;

/**
 * RBAC role endpoints (spec tag "RBACRoleEndpoints"): `/rbac_role_endpoints` and `/rbac_role_endpoints/{RBACRoleEndpointId}`.
 */
final readonly class RbacRoleEndpoints extends AbstractResource
{
    private const string SEGMENT = 'rbac_role_endpoints';

    /**
     * List one page of RBAC role endpoints (operationId `list-rbac_role_endpoint`).
     *
     * @return Page<RbacRoleEndpoint>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/rbac_role_endpoints', 'list-rbac_role_endpoint', OperationScope::GlobalOnly)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::GlobalOnly, self::SEGMENT), RbacRoleEndpoint::fromArray(...), $options);
    }

    /**
     * Lazily iterate every RBAC role endpoint across all pages (operationId `list-rbac_role_endpoint`).
     *
     * @return Generator<int, RbacRoleEndpoint>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/rbac_role_endpoints', 'list-rbac_role_endpoint', OperationScope::GlobalOnly)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::GlobalOnly, self::SEGMENT), RbacRoleEndpoint::fromArray(...), $options);
    }

    /**
     * The page after $page, or null when $page is the last (operationId `list-rbac_role_endpoint`).
     *
     * Repeats list() with the page's `offset`, keeping the same filters and workspace. Kong's own `next`
     * link is not followed (it drops filters on nested lists; spec-notes Q6).
     *
     * @param Page<RbacRoleEndpoint> $page
     *
     * @return Page<RbacRoleEndpoint>|null
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/rbac_role_endpoints', 'list-rbac_role_endpoint', OperationScope::GlobalOnly)]
    public function nextPage(Page $page, ?ListOptions $options = null): ?Page
    {
        return $page->offset === null ? null : $this->list(($options ?? new ListOptions())->withOffset($page->offset));
    }

    /**
     * Get an RBAC role endpoint by ID (operationId `get-rbac_role_endpoint`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/rbac_role_endpoints/{RBACRoleEndpointId}', 'get-rbac_role_endpoint', OperationScope::GlobalOnly)]
    public function get(string $id): RbacRoleEndpoint
    {
        return RbacRoleEndpoint::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id),
        ));
    }

    /**
     * Create an RBAC role endpoint (operationId `create-rbac_role_endpoint`, body `RBACRoleEndpoint`).
     *
     * @param RbacRoleEndpointInput|array<string, mixed> $rbacRoleEndpoint
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/rbac_role_endpoints', 'create-rbac_role_endpoint', OperationScope::GlobalOnly)]
    public function create(RbacRoleEndpointInput|array $rbacRoleEndpoint): RbacRoleEndpoint
    {
        return RbacRoleEndpoint::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT),
            body: $rbacRoleEndpoint,
        ));
    }

    /**
     * Update fields of an RBAC role endpoint (operationId `update-rbac_role_endpoint`, PATCH, body `RBACRoleEndpoint`).
     *
     * @param RbacRoleEndpointInput|array<string, mixed> $rbacRoleEndpoint only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/rbac_role_endpoints/{RBACRoleEndpointId}', 'update-rbac_role_endpoint', OperationScope::GlobalOnly)]
    public function update(string $id, RbacRoleEndpointInput|array $rbacRoleEndpoint): RbacRoleEndpoint
    {
        return RbacRoleEndpoint::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id),
            body: $rbacRoleEndpoint,
        ));
    }

    /**
     * Create or replace an RBAC role endpoint by ID (operationId `upsert-rbac_role_endpoint`, PUT, body `RBACRoleEndpoint`).
     *
     * @param RbacRoleEndpointInput|array<string, mixed> $rbacRoleEndpoint
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/rbac_role_endpoints/{RBACRoleEndpointId}', 'upsert-rbac_role_endpoint', OperationScope::GlobalOnly)]
    public function upsert(string $id, RbacRoleEndpointInput|array $rbacRoleEndpoint): RbacRoleEndpoint
    {
        return RbacRoleEndpoint::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id),
            body: $rbacRoleEndpoint,
        ));
    }

    /**
     * Delete an RBAC role endpoint (operationId `delete-rbac_role_endpoint`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/rbac_role_endpoints/{RBACRoleEndpointId}', 'delete-rbac_role_endpoint', OperationScope::GlobalOnly)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id));
    }
}
