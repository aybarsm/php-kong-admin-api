<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources\Nested;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\RbacRoleEndpoint;
use Aybarsm\Kong\AdminApi\Models\RbacRoleEndpointInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;
use Generator;

/**
 * Endpoint permissions of one RBAC role (spec tag "RBACRoleEndpoints"):
 * `/{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/endpoints`.
 *
 * Only list and create are implemented: the spec's item path
 * `…/endpoints/{workspace}{RBACRoleEndpointId}` is malformed and blocked (spec-notes Q2). Use the global
 * `$client->rbacRoleEndpoints()` for single-item operations. These paths exist only under `/{workspace}`;
 * without a workspace the spec default `default` is used.
 * Obtain it with `$client->workspaceRbacRoles()->endpoints($roleId)`.
 */
final readonly class WorkspaceRbacRoleEndpoints extends AbstractResource
{
    private const string PATH = '/{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/endpoints';

    private const string SEGMENT = 'endpoints';

    /**
     * List one page of the role's endpoint permissions (operationId `list-rbac_role_endpoint-in-workspace`).
     *
     * @return Page<RbacRoleEndpoint>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, self::PATH, 'list-rbac_role_endpoint-in-workspace', OperationScope::WorkspaceOnly)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::WorkspaceOnly, self::SEGMENT), RbacRoleEndpoint::fromArray(...), $options);
    }

    /**
     * Lazily iterate every endpoint permission of the role (operationId `list-rbac_role_endpoint-in-workspace`).
     *
     * @return Generator<int, RbacRoleEndpoint>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, self::PATH, 'list-rbac_role_endpoint-in-workspace', OperationScope::WorkspaceOnly)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::WorkspaceOnly, self::SEGMENT), RbacRoleEndpoint::fromArray(...), $options);
    }

    /**
     * The page after $page, or null when $page is the last (operationId `list-rbac_role_endpoint-in-workspace`).
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
    #[Operation(Transport::METHOD_GET, self::PATH, 'list-rbac_role_endpoint-in-workspace', OperationScope::WorkspaceOnly)]
    public function nextPage(Page $page, ?ListOptions $options = null): ?Page
    {
        return $page->offset === null ? null : $this->list(($options ?? new ListOptions())->withOffset($page->offset));
    }

    /**
     * Add an endpoint permission (operationId `create-rbac_role_endpoint-in-workspace`, body `RBACRoleEndpoint`).
     *
     * @param RbacRoleEndpointInput|array<string, mixed> $endpoint
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, self::PATH, 'create-rbac_role_endpoint-in-workspace', OperationScope::WorkspaceOnly)]
    public function create(RbacRoleEndpointInput|array $endpoint): RbacRoleEndpoint
    {
        return RbacRoleEndpoint::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::WorkspaceOnly, self::SEGMENT),
            body: $endpoint,
        ));
    }
}
