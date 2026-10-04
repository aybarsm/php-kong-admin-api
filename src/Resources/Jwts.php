<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\Jwt;
use Aybarsm\Kong\AdminApi\Models\JwtInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Generator;

/**
 * JWTs (spec tag "JWTs"): `/jwts` and `/jwts/{JWTId}`.
 */
final readonly class Jwts extends AbstractResource
{
    private const string SEGMENT = 'jwts';

    /**
     * List one page of JWTs (operationId `list-jwt`).
     *
     * @return Page<Jwt>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/jwts', 'list-jwt', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), Jwt::fromArray(...), $options);
    }

    /**
     * Lazily iterate every JWT across all pages (operationId `list-jwt`).
     *
     * @return Generator<int, Jwt>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/jwts', 'list-jwt', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), Jwt::fromArray(...), $options);
    }

    /**
     * Get a JWT by ID (operationId `get-jwt`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/jwts/{JWTId}', 'get-jwt', OperationScope::Both)]
    public function get(string $id): Jwt
    {
        return Jwt::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
        ));
    }

    /**
     * Create a JWT (operationId `create-jwt`, body `JWT`).
     *
     * @param JwtInput|array<string, mixed> $jwt
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/jwts', 'create-jwt', OperationScope::Both)]
    public function create(JwtInput|array $jwt): Jwt
    {
        return Jwt::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $jwt,
        ));
    }

    /**
     * Update fields of a JWT (operationId `update-jwt`, PATCH, body `JWT`).
     *
     * @param JwtInput|array<string, mixed> $jwt only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/jwts/{JWTId}', 'update-jwt', OperationScope::Both)]
    public function update(string $id, JwtInput|array $jwt): Jwt
    {
        return Jwt::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $jwt,
        ));
    }

    /**
     * Create or replace a JWT by ID (operationId `upsert-jwt`, PUT, body `JWT`).
     *
     * @param JwtInput|array<string, mixed> $jwt
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/jwts/{JWTId}', 'upsert-jwt', OperationScope::Both)]
    public function upsert(string $id, JwtInput|array $jwt): Jwt
    {
        return Jwt::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $jwt,
        ));
    }

    /**
     * Delete a JWT (operationId `delete-jwt`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/jwts/{JWTId}', 'delete-jwt', OperationScope::Both)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $id));
    }
}
