<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\Acl;
use Aybarsm\Kong\AdminApi\Models\AclInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Generator;

/**
 * ACLs (spec tag "ACLs"): `/acls` and `/acls/{ACLId}`.
 */
final readonly class Acls extends AbstractResource
{
    private const string SEGMENT = 'acls';

    /**
     * List one page of ACLs (operationId `list-acl`).
     *
     * @return Page<Acl>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/acls', 'list-acl', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), Acl::fromArray(...), $options);
    }

    /**
     * Lazily iterate every ACL across all pages (operationId `list-acl`).
     *
     * @return Generator<int, Acl>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/acls', 'list-acl', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), Acl::fromArray(...), $options);
    }

    /**
     * The page after $page, or null when $page is the last (operationId `list-acl`).
     *
     * Repeats list() with the page's `offset`, keeping the same filters and workspace. Kong's own `next`
     * link is not followed (it drops filters on nested lists; spec-notes Q6).
     *
     * @param Page<Acl> $page
     *
     * @return Page<Acl>|null
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/acls', 'list-acl', OperationScope::Both)]
    public function nextPage(Page $page, ?ListOptions $options = null): ?Page
    {
        return $page->offset === null ? null : $this->list(($options ?? new ListOptions())->withOffset($page->offset));
    }

    /**
     * Get an ACL by ID (operationId `get-acl`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/acls/{ACLId}', 'get-acl', OperationScope::Both)]
    public function get(string $id): Acl
    {
        return Acl::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
        ));
    }

    /**
     * Create an ACL (operationId `create-acl`, body `ACL`).
     *
     * @param AclInput|array<string, mixed> $acl
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/acls', 'create-acl', OperationScope::Both)]
    public function create(AclInput|array $acl): Acl
    {
        return Acl::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $acl,
        ));
    }

    /**
     * Update fields of an ACL (operationId `update-acl`, PATCH, body `ACL`).
     *
     * @param AclInput|array<string, mixed> $acl only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/acls/{ACLId}', 'update-acl', OperationScope::Both)]
    public function update(string $id, AclInput|array $acl): Acl
    {
        return Acl::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $acl,
        ));
    }

    /**
     * Create or replace an ACL by ID (operationId `upsert-acl`, PUT, body `ACL`).
     *
     * @param AclInput|array<string, mixed> $acl
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/acls/{ACLId}', 'upsert-acl', OperationScope::Both)]
    public function upsert(string $id, AclInput|array $acl): Acl
    {
        return Acl::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $acl,
        ));
    }

    /**
     * Delete an ACL (operationId `delete-acl`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/acls/{ACLId}', 'delete-acl', OperationScope::Both)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $id));
    }
}
