<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources\Nested;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\RbacUserRole;
use Aybarsm\Kong\AdminApi\Models\RbacUserRoleInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;
use Generator;

/**
 * Roles of one RBAC user (spec tag "RBACUserRoles"): `/{workspace}/rbac/users/{RBACUserIdForNestedEntities}/roles`.
 *
 * These paths exist only under `/{workspace}`; without a workspace the spec default `default` is used.
 * Obtain it with `$client->workspaceRbacUsers()->roles($userId)`.
 */
final readonly class WorkspaceRbacUserRoles extends AbstractResource
{
    private const string PATH = '/{workspace}/rbac/users/{RBACUserIdForNestedEntities}/roles';

    private const string SEGMENT = 'roles';

    /**
     * List one page of the user's roles (operationId `list-rbac_user_role-in-workspace`).
     *
     * @return Page<RbacUserRole>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, self::PATH, 'list-rbac_user_role-in-workspace', OperationScope::WorkspaceOnly)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::WorkspaceOnly, self::SEGMENT), RbacUserRole::fromArray(...), $options);
    }

    /**
     * Lazily iterate every role of the user (operationId `list-rbac_user_role-in-workspace`).
     *
     * @return Generator<int, RbacUserRole>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, self::PATH, 'list-rbac_user_role-in-workspace', OperationScope::WorkspaceOnly)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::WorkspaceOnly, self::SEGMENT), RbacUserRole::fromArray(...), $options);
    }

    /**
     * Assign a role to the user (operationId `create-rbac_user_role-in-workspace`, body `RBACUserRole`).
     *
     * @param RbacUserRoleInput|array<string, mixed> $role
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, self::PATH, 'create-rbac_user_role-in-workspace', OperationScope::WorkspaceOnly)]
    public function add(RbacUserRoleInput|array $role): RbacUserRole
    {
        return RbacUserRole::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::WorkspaceOnly, self::SEGMENT),
            body: $role,
        ));
    }

    /**
     * Remove roles from the user (operationId `delete-rbac_user_role-in-workspace`). The spec sends the roles
     * to remove as a JSON body (`RBACUserRole`) on this DELETE.
     *
     * @param RbacUserRoleInput|array<string, mixed> $role
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_DELETE, self::PATH, 'delete-rbac_user_role-in-workspace', OperationScope::WorkspaceOnly)]
    public function remove(RbacUserRoleInput|array $role): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::WorkspaceOnly, self::SEGMENT), body: $role);
    }
}
