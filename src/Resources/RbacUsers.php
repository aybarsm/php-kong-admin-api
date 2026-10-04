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
use Generator;

/**
 * RBAC users (spec tag "RBACUsers"): `/rbac_users` and `/rbac_users/{RBACUserId}`.
 */
final readonly class RbacUsers extends AbstractResource
{
    private const string SEGMENT = 'rbac_users';

    /**
     * List one page of RBAC users (operationId `list-rbac_user`).
     *
     * @return Page<RbacUser>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/rbac_users', 'list-rbac_user', OperationScope::GlobalOnly)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::GlobalOnly, self::SEGMENT), RbacUser::fromArray(...), $options);
    }

    /**
     * Lazily iterate every RBAC user across all pages (operationId `list-rbac_user`).
     *
     * @return Generator<int, RbacUser>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/rbac_users', 'list-rbac_user', OperationScope::GlobalOnly)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::GlobalOnly, self::SEGMENT), RbacUser::fromArray(...), $options);
    }

    /**
     * Get an RBAC user by ID (operationId `get-rbac_user`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/rbac_users/{RBACUserId}', 'get-rbac_user', OperationScope::GlobalOnly)]
    public function get(string $id): RbacUser
    {
        return RbacUser::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id),
        ));
    }

    /**
     * Create an RBAC user (operationId `create-rbac_user`, body `RBACUser`).
     *
     * @param RbacUserInput|array<string, mixed> $rbacUser
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/rbac_users', 'create-rbac_user', OperationScope::GlobalOnly)]
    public function create(RbacUserInput|array $rbacUser): RbacUser
    {
        return RbacUser::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT),
            body: $rbacUser,
        ));
    }

    /**
     * Update fields of an RBAC user (operationId `update-rbac_user`, PATCH, body `RBACUser`).
     *
     * @param RbacUserInput|array<string, mixed> $rbacUser only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/rbac_users/{RBACUserId}', 'update-rbac_user', OperationScope::GlobalOnly)]
    public function update(string $id, RbacUserInput|array $rbacUser): RbacUser
    {
        return RbacUser::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id),
            body: $rbacUser,
        ));
    }

    /**
     * Create or replace an RBAC user by ID (operationId `upsert-rbac_user`, PUT, body `RBACUser`).
     *
     * @param RbacUserInput|array<string, mixed> $rbacUser
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/rbac_users/{RBACUserId}', 'upsert-rbac_user', OperationScope::GlobalOnly)]
    public function upsert(string $id, RbacUserInput|array $rbacUser): RbacUser
    {
        return RbacUser::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id),
            body: $rbacUser,
        ));
    }

    /**
     * Delete an RBAC user (operationId `delete-rbac_user`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/rbac_users/{RBACUserId}', 'delete-rbac_user', OperationScope::GlobalOnly)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id));
    }
}
