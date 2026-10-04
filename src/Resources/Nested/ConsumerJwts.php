<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources\Nested;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\Jwt;
use Aybarsm\Kong\AdminApi\Models\JwtInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;
use Generator;

/**
 * JWTs nested under one Consumer: `/consumers/{ConsumerIdForNestedEntities}/jwt` and `/consumers/{ConsumerIdForNestedEntities}/jwt/{JWTId}`.
 *
 * Obtain it with `$client->consumers()->jwts($consumerId)`.
 */
final readonly class ConsumerJwts extends AbstractResource
{
    private const string SEGMENT = 'jwt';

    /**
     * List one page of JWTs (operationId `list-jwt-with-consumer`).
     *
     * @return Page<Jwt>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/consumers/{ConsumerIdForNestedEntities}/jwt', 'list-jwt-with-consumer', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), Jwt::fromArray(...), $options);
    }

    /**
     * Lazily iterate every JWT across all pages (operationId `list-jwt-with-consumer`).
     *
     * @return Generator<int, Jwt>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/consumers/{ConsumerIdForNestedEntities}/jwt', 'list-jwt-with-consumer', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), Jwt::fromArray(...), $options);
    }

    /**
     * Get a JWT by ID (operationId `get-jwt-with-consumer`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/consumers/{ConsumerIdForNestedEntities}/jwt/{JWTId}', 'get-jwt-with-consumer', OperationScope::Both)]
    public function get(string $id): Jwt
    {
        return Jwt::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
        ));
    }

    /**
     * Create a JWT (operationId `create-jwt-with-consumer`, body `JWTWithoutParents`).
     *
     * @param JwtInput|array<string, mixed> $jwt
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/consumers/{ConsumerIdForNestedEntities}/jwt', 'create-jwt-with-consumer', OperationScope::Both)]
    public function create(JwtInput|array $jwt): Jwt
    {
        return Jwt::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $jwt,
        ));
    }

    /**
     * Update fields of a JWT (operationId `update-jwt-with-consumer`, PATCH, body `JWT`).
     *
     * @param JwtInput|array<string, mixed> $jwt only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/consumers/{ConsumerIdForNestedEntities}/jwt/{JWTId}', 'update-jwt-with-consumer', OperationScope::Both)]
    public function update(string $id, JwtInput|array $jwt): Jwt
    {
        return Jwt::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $jwt,
        ));
    }

    /**
     * Create or replace a JWT by ID (operationId `upsert-jwt-with-consumer`, PUT, body `JWTWithoutParents`).
     *
     * @param JwtInput|array<string, mixed> $jwt
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/consumers/{ConsumerIdForNestedEntities}/jwt/{JWTId}', 'upsert-jwt-with-consumer', OperationScope::Both)]
    public function upsert(string $id, JwtInput|array $jwt): Jwt
    {
        return Jwt::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $jwt,
        ));
    }

    /**
     * Delete a JWT (operationId `delete-jwt-with-consumer`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/consumers/{ConsumerIdForNestedEntities}/jwt/{JWTId}', 'delete-jwt-with-consumer', OperationScope::Both)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $id));
    }
}
