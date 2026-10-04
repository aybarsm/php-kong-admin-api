<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources\Nested;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\MtlsAuth;
use Aybarsm\Kong\AdminApi\Models\MtlsAuthInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;
use Generator;

/**
 * MTLS-auth credentials nested under one Consumer: `/consumers/{ConsumerIdForNestedEntities}/mtls-auth` and `/consumers/{ConsumerIdForNestedEntities}/mtls-auth/{MTLSAuthId}`.
 *
 * Obtain it with `$client->consumers()->mtlsAuths($consumerId)`.
 */
final readonly class ConsumerMtlsAuths extends AbstractResource
{
    private const string SEGMENT = 'mtls-auth';

    /**
     * List one page of MTLS-auth credentials (operationId `list-mtls-auth-with-consumer`).
     *
     * @return Page<MtlsAuth>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/consumers/{ConsumerIdForNestedEntities}/mtls-auth', 'list-mtls-auth-with-consumer', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), MtlsAuth::fromArray(...), $options);
    }

    /**
     * Lazily iterate every MTLS-auth credential across all pages (operationId `list-mtls-auth-with-consumer`).
     *
     * @return Generator<int, MtlsAuth>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/consumers/{ConsumerIdForNestedEntities}/mtls-auth', 'list-mtls-auth-with-consumer', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), MtlsAuth::fromArray(...), $options);
    }

    /**
     * The page after $page, or null when $page is the last (operationId `list-mtls-auth-with-consumer`).
     *
     * Repeats list() with the page's `offset`, keeping the same filters and workspace. Kong's own `next`
     * link is not followed (it drops filters on nested lists; spec-notes Q6).
     *
     * @param Page<MtlsAuth> $page
     *
     * @return Page<MtlsAuth>|null
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/consumers/{ConsumerIdForNestedEntities}/mtls-auth', 'list-mtls-auth-with-consumer', OperationScope::Both)]
    public function nextPage(Page $page, ?ListOptions $options = null): ?Page
    {
        return $page->offset === null ? null : $this->list(($options ?? new ListOptions())->withOffset($page->offset));
    }

    /**
     * Get an MTLS-auth credential by ID (operationId `get-mtls-auth-with-consumer`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/consumers/{ConsumerIdForNestedEntities}/mtls-auth/{MTLSAuthId}', 'get-mtls-auth-with-consumer', OperationScope::Both)]
    public function get(string $id): MtlsAuth
    {
        return MtlsAuth::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
        ));
    }

    /**
     * Create an MTLS-auth credential (operationId `create-mtls-auth-with-consumer`, body `MTLSAuthWithoutParents`).
     *
     * @param MtlsAuthInput|array<string, mixed> $mtlsAuth
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/consumers/{ConsumerIdForNestedEntities}/mtls-auth', 'create-mtls-auth-with-consumer', OperationScope::Both)]
    public function create(MtlsAuthInput|array $mtlsAuth): MtlsAuth
    {
        return MtlsAuth::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $mtlsAuth,
        ));
    }

    /**
     * Update fields of an MTLS-auth credential (operationId `update-mtls-auth-with-consumer`, PATCH, body `MTLSAuth`).
     *
     * @param MtlsAuthInput|array<string, mixed> $mtlsAuth only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/consumers/{ConsumerIdForNestedEntities}/mtls-auth/{MTLSAuthId}', 'update-mtls-auth-with-consumer', OperationScope::Both)]
    public function update(string $id, MtlsAuthInput|array $mtlsAuth): MtlsAuth
    {
        return MtlsAuth::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $mtlsAuth,
        ));
    }

    /**
     * Create or replace an MTLS-auth credential by ID (operationId `upsert-mtls-auth-with-consumer`, PUT, body `MTLSAuthWithoutParents`).
     *
     * @param MtlsAuthInput|array<string, mixed> $mtlsAuth
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/consumers/{ConsumerIdForNestedEntities}/mtls-auth/{MTLSAuthId}', 'upsert-mtls-auth-with-consumer', OperationScope::Both)]
    public function upsert(string $id, MtlsAuthInput|array $mtlsAuth): MtlsAuth
    {
        return MtlsAuth::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $mtlsAuth,
        ));
    }

    /**
     * Delete an MTLS-auth credential (operationId `delete-mtls-auth-with-consumer`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/consumers/{ConsumerIdForNestedEntities}/mtls-auth/{MTLSAuthId}', 'delete-mtls-auth-with-consumer', OperationScope::Both)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $id));
    }
}
