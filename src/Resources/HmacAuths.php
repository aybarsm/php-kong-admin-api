<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\HmacAuth;
use Aybarsm\Kong\AdminApi\Models\HmacAuthInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Generator;

/**
 * HMAC-auth credentials (spec tag "HMAC-auth credentials"): `/hmac-auths` and `/hmac-auths/{HMACAuthId}`.
 */
final readonly class HmacAuths extends AbstractResource
{
    private const string SEGMENT = 'hmac-auths';

    /**
     * List one page of HMAC-auth credentials (operationId `list-hmac-auth`).
     *
     * @return Page<HmacAuth>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/hmac-auths', 'list-hmac-auth', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), HmacAuth::fromArray(...), $options);
    }

    /**
     * Lazily iterate every HMAC-auth credential across all pages (operationId `list-hmac-auth`).
     *
     * @return Generator<int, HmacAuth>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/hmac-auths', 'list-hmac-auth', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), HmacAuth::fromArray(...), $options);
    }

    /**
     * Get an HMAC-auth credential by ID (operationId `get-hmac-auth`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/hmac-auths/{HMACAuthId}', 'get-hmac-auth', OperationScope::Both)]
    public function get(string $id): HmacAuth
    {
        return HmacAuth::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
        ));
    }

    /**
     * Create an HMAC-auth credential (operationId `create-hmac-auth`, body `HMACAuth`).
     *
     * @param HmacAuthInput|array<string, mixed> $hmacAuth
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/hmac-auths', 'create-hmac-auth', OperationScope::Both)]
    public function create(HmacAuthInput|array $hmacAuth): HmacAuth
    {
        return HmacAuth::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $hmacAuth,
        ));
    }

    /**
     * Update fields of an HMAC-auth credential (operationId `update-hmac-auth`, PATCH, body `HMACAuth`).
     *
     * @param HmacAuthInput|array<string, mixed> $hmacAuth only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/hmac-auths/{HMACAuthId}', 'update-hmac-auth', OperationScope::Both)]
    public function update(string $id, HmacAuthInput|array $hmacAuth): HmacAuth
    {
        return HmacAuth::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $hmacAuth,
        ));
    }

    /**
     * Create or replace an HMAC-auth credential by ID (operationId `upsert-hmac-auth`, PUT, body `HMACAuth`).
     *
     * @param HmacAuthInput|array<string, mixed> $hmacAuth
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/hmac-auths/{HMACAuthId}', 'upsert-hmac-auth', OperationScope::Both)]
    public function upsert(string $id, HmacAuthInput|array $hmacAuth): HmacAuth
    {
        return HmacAuth::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $hmacAuth,
        ));
    }

    /**
     * Delete an HMAC-auth credential (operationId `delete-hmac-auth`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/hmac-auths/{HMACAuthId}', 'delete-hmac-auth', OperationScope::Both)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $id));
    }
}
