<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources\Nested;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\KeyAuth;
use Aybarsm\Kong\AdminApi\Models\KeyAuthInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;
use Generator;

/**
 * API keys nested under one Consumer: `/consumers/{ConsumerIdForNestedEntities}/key-auth` and `/consumers/{ConsumerIdForNestedEntities}/key-auth/{KeyAuthId}`.
 *
 * Obtain it with `$client->consumers()->keyAuths($consumerId)`.
 */
final readonly class ConsumerKeyAuths extends AbstractResource
{
    private const string SEGMENT = 'key-auth';

    /**
     * List one page of API keys (operationId `list-key-auth-with-consumer`).
     *
     * @return Page<KeyAuth>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/consumers/{ConsumerIdForNestedEntities}/key-auth', 'list-key-auth-with-consumer', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), KeyAuth::fromArray(...), $options);
    }

    /**
     * Lazily iterate every API key across all pages (operationId `list-key-auth-with-consumer`).
     *
     * @return Generator<int, KeyAuth>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/consumers/{ConsumerIdForNestedEntities}/key-auth', 'list-key-auth-with-consumer', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), KeyAuth::fromArray(...), $options);
    }

    /**
     * Get an API key by ID (operationId `get-key-auth-with-consumer`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/consumers/{ConsumerIdForNestedEntities}/key-auth/{KeyAuthId}', 'get-key-auth-with-consumer', OperationScope::Both)]
    public function get(string $id): KeyAuth
    {
        return KeyAuth::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
        ));
    }

    /**
     * Create an API key (operationId `create-key-auth-with-consumer`, body `KeyAuthWithoutParents`).
     *
     * @param KeyAuthInput|array<string, mixed> $keyAuth
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/consumers/{ConsumerIdForNestedEntities}/key-auth', 'create-key-auth-with-consumer', OperationScope::Both)]
    public function create(KeyAuthInput|array $keyAuth): KeyAuth
    {
        return KeyAuth::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $keyAuth,
        ));
    }

    /**
     * Update fields of an API key (operationId `update-key-auth-with-consumer`, PATCH, body `KeyAuth`).
     *
     * @param KeyAuthInput|array<string, mixed> $keyAuth only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/consumers/{ConsumerIdForNestedEntities}/key-auth/{KeyAuthId}', 'update-key-auth-with-consumer', OperationScope::Both)]
    public function update(string $id, KeyAuthInput|array $keyAuth): KeyAuth
    {
        return KeyAuth::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $keyAuth,
        ));
    }

    /**
     * Create or replace an API key by ID (operationId `upsert-key-auth-with-consumer`, PUT, body `KeyAuthWithoutParents`).
     *
     * @param KeyAuthInput|array<string, mixed> $keyAuth
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/consumers/{ConsumerIdForNestedEntities}/key-auth/{KeyAuthId}', 'upsert-key-auth-with-consumer', OperationScope::Both)]
    public function upsert(string $id, KeyAuthInput|array $keyAuth): KeyAuth
    {
        return KeyAuth::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $keyAuth,
        ));
    }

    /**
     * Delete an API key (operationId `delete-key-auth-with-consumer`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/consumers/{ConsumerIdForNestedEntities}/key-auth/{KeyAuthId}', 'delete-key-auth-with-consumer', OperationScope::Both)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $id));
    }
}
