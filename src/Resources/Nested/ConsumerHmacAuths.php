<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources\Nested;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\HmacAuth;
use Aybarsm\Kong\AdminApi\Models\HmacAuthInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;
use Generator;

/**
 * HMAC-auth credentials nested under one Consumer: `/consumers/{ConsumerIdForNestedEntities}/hmac-auth` and `/consumers/{ConsumerIdForNestedEntities}/hmac-auth/{HMACAuthId}`.
 *
 * Obtain it with `$client->consumers()->hmacAuths($consumerId)`.
 */
final readonly class ConsumerHmacAuths extends AbstractResource
{
    private const string SEGMENT = 'hmac-auth';

    /**
     * List one page of HMAC-auth credentials (operationId `list-hmac-auth-with-consumer`).
     *
     * @return Page<HmacAuth>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/consumers/{ConsumerIdForNestedEntities}/hmac-auth', 'list-hmac-auth-with-consumer', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), HmacAuth::fromArray(...), $options);
    }

    /**
     * Lazily iterate every HMAC-auth credential across all pages (operationId `list-hmac-auth-with-consumer`).
     *
     * @return Generator<int, HmacAuth>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/consumers/{ConsumerIdForNestedEntities}/hmac-auth', 'list-hmac-auth-with-consumer', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), HmacAuth::fromArray(...), $options);
    }

    /**
     * The page after $page, or null when $page is the last (operationId `list-hmac-auth-with-consumer`).
     *
     * Repeats list() with the page's `offset`, keeping the same filters and workspace. Kong's own `next`
     * link is not followed (it drops filters on nested lists; spec-notes Q6).
     *
     * @param Page<HmacAuth> $page
     *
     * @return Page<HmacAuth>|null
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/consumers/{ConsumerIdForNestedEntities}/hmac-auth', 'list-hmac-auth-with-consumer', OperationScope::Both)]
    public function nextPage(Page $page, ?ListOptions $options = null): ?Page
    {
        return $page->offset === null ? null : $this->list(($options ?? new ListOptions())->withOffset($page->offset));
    }

    /**
     * Get an HMAC-auth credential by ID (operationId `get-hmac-auth-with-consumer`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/consumers/{ConsumerIdForNestedEntities}/hmac-auth/{HMACAuthId}', 'get-hmac-auth-with-consumer', OperationScope::Both)]
    public function get(string $id): HmacAuth
    {
        return HmacAuth::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
        ));
    }

    /**
     * Create an HMAC-auth credential (operationId `create-hmac-auth-with-consumer`, body `HMACAuthWithoutParents`).
     *
     * @param HmacAuthInput|array<string, mixed> $hmacAuth
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/consumers/{ConsumerIdForNestedEntities}/hmac-auth', 'create-hmac-auth-with-consumer', OperationScope::Both)]
    public function create(HmacAuthInput|array $hmacAuth): HmacAuth
    {
        return HmacAuth::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $hmacAuth,
        ));
    }

    /**
     * Update fields of an HMAC-auth credential (operationId `update-hmac-auth-with-consumer`, PATCH, body `HMACAuth`).
     *
     * @param HmacAuthInput|array<string, mixed> $hmacAuth only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/consumers/{ConsumerIdForNestedEntities}/hmac-auth/{HMACAuthId}', 'update-hmac-auth-with-consumer', OperationScope::Both)]
    public function update(string $id, HmacAuthInput|array $hmacAuth): HmacAuth
    {
        return HmacAuth::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $hmacAuth,
        ));
    }

    /**
     * Create or replace an HMAC-auth credential by ID (operationId `upsert-hmac-auth-with-consumer`, PUT, body `HMACAuthWithoutParents`).
     *
     * @param HmacAuthInput|array<string, mixed> $hmacAuth
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/consumers/{ConsumerIdForNestedEntities}/hmac-auth/{HMACAuthId}', 'upsert-hmac-auth-with-consumer', OperationScope::Both)]
    public function upsert(string $id, HmacAuthInput|array $hmacAuth): HmacAuth
    {
        return HmacAuth::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $hmacAuth,
        ));
    }

    /**
     * Delete an HMAC-auth credential (operationId `delete-hmac-auth-with-consumer`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/consumers/{ConsumerIdForNestedEntities}/hmac-auth/{HMACAuthId}', 'delete-hmac-auth-with-consumer', OperationScope::Both)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $id));
    }
}
