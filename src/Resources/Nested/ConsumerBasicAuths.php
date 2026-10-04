<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources\Nested;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\BasicAuth;
use Aybarsm\Kong\AdminApi\Models\BasicAuthInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;
use Generator;

/**
 * Basic-auth credentials nested under one Consumer: `/consumers/{ConsumerIdForNestedEntities}/basic-auth` and `/consumers/{ConsumerIdForNestedEntities}/basic-auth/{BasicAuthId}`.
 *
 * Obtain it with `$client->consumers()->basicAuths($consumerId)`.
 */
final readonly class ConsumerBasicAuths extends AbstractResource
{
    private const string SEGMENT = 'basic-auth';

    /**
     * List one page of Basic-auth credentials (operationId `list-basic-auth-with-consumer`).
     *
     * @return Page<BasicAuth>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/consumers/{ConsumerIdForNestedEntities}/basic-auth', 'list-basic-auth-with-consumer', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), BasicAuth::fromArray(...), $options);
    }

    /**
     * Lazily iterate every Basic-auth credential across all pages (operationId `list-basic-auth-with-consumer`).
     *
     * @return Generator<int, BasicAuth>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/consumers/{ConsumerIdForNestedEntities}/basic-auth', 'list-basic-auth-with-consumer', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), BasicAuth::fromArray(...), $options);
    }

    /**
     * The page after $page, or null when $page is the last (operationId `list-basic-auth-with-consumer`).
     *
     * Repeats list() with the page's `offset`, keeping the same filters and workspace. Kong's own `next`
     * link is not followed (it drops filters on nested lists; spec-notes Q6).
     *
     * @param Page<BasicAuth> $page
     *
     * @return Page<BasicAuth>|null
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/consumers/{ConsumerIdForNestedEntities}/basic-auth', 'list-basic-auth-with-consumer', OperationScope::Both)]
    public function nextPage(Page $page, ?ListOptions $options = null): ?Page
    {
        return $page->offset === null ? null : $this->list(($options ?? new ListOptions())->withOffset($page->offset));
    }

    /**
     * Get a Basic-auth credential by ID (operationId `get-basic-auth-with-consumer`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/consumers/{ConsumerIdForNestedEntities}/basic-auth/{BasicAuthId}', 'get-basic-auth-with-consumer', OperationScope::Both)]
    public function get(string $id): BasicAuth
    {
        return BasicAuth::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
        ));
    }

    /**
     * Create a Basic-auth credential (operationId `create-basic-auth-with-consumer`, body `BasicAuthWithoutParents`).
     *
     * @param BasicAuthInput|array<string, mixed> $basicAuth
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/consumers/{ConsumerIdForNestedEntities}/basic-auth', 'create-basic-auth-with-consumer', OperationScope::Both)]
    public function create(BasicAuthInput|array $basicAuth): BasicAuth
    {
        return BasicAuth::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $basicAuth,
        ));
    }

    /**
     * Update fields of a Basic-auth credential (operationId `update-basic-auth-with-consumer`, PATCH, body `BasicAuth`).
     *
     * @param BasicAuthInput|array<string, mixed> $basicAuth only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/consumers/{ConsumerIdForNestedEntities}/basic-auth/{BasicAuthId}', 'update-basic-auth-with-consumer', OperationScope::Both)]
    public function update(string $id, BasicAuthInput|array $basicAuth): BasicAuth
    {
        return BasicAuth::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $basicAuth,
        ));
    }

    /**
     * Create or replace a Basic-auth credential by ID (operationId `upsert-basic-auth-with-consumer`, PUT, body `BasicAuthWithoutParents`).
     *
     * @param BasicAuthInput|array<string, mixed> $basicAuth
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/consumers/{ConsumerIdForNestedEntities}/basic-auth/{BasicAuthId}', 'upsert-basic-auth-with-consumer', OperationScope::Both)]
    public function upsert(string $id, BasicAuthInput|array $basicAuth): BasicAuth
    {
        return BasicAuth::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $basicAuth,
        ));
    }

    /**
     * Delete a Basic-auth credential (operationId `delete-basic-auth-with-consumer`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/consumers/{ConsumerIdForNestedEntities}/basic-auth/{BasicAuthId}', 'delete-basic-auth-with-consumer', OperationScope::Both)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $id));
    }
}
