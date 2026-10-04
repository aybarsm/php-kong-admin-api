<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\MtlsAuth;
use Aybarsm\Kong\AdminApi\Models\MtlsAuthInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Generator;

/**
 * MTLS-auth credentials (spec tag "MTLS-auth credentials"): `/mtls-auths` and `/mtls-auths/{MTLSAuthId}`.
 */
final readonly class MtlsAuths extends AbstractResource
{
    private const string SEGMENT = 'mtls-auths';

    /**
     * List one page of MTLS-auth credentials (operationId `list-mtls-auth`).
     *
     * @return Page<MtlsAuth>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/mtls-auths', 'list-mtls-auth', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), MtlsAuth::fromArray(...), $options);
    }

    /**
     * Lazily iterate every MTLS-auth credential across all pages (operationId `list-mtls-auth`).
     *
     * @return Generator<int, MtlsAuth>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/mtls-auths', 'list-mtls-auth', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), MtlsAuth::fromArray(...), $options);
    }

    /**
     * Get an MTLS-auth credential by ID (operationId `get-mtls-auth`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/mtls-auths/{MTLSAuthId}', 'get-mtls-auth', OperationScope::Both)]
    public function get(string $id): MtlsAuth
    {
        return MtlsAuth::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
        ));
    }

    /**
     * Create an MTLS-auth credential (operationId `create-mtls-auth`, body `MTLSAuth`).
     *
     * @param MtlsAuthInput|array<string, mixed> $mtlsAuth
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/mtls-auths', 'create-mtls-auth', OperationScope::Both)]
    public function create(MtlsAuthInput|array $mtlsAuth): MtlsAuth
    {
        return MtlsAuth::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $mtlsAuth,
        ));
    }

    /**
     * Update fields of an MTLS-auth credential (operationId `update-mtls-auth`, PATCH, body `MTLSAuth`).
     *
     * @param MtlsAuthInput|array<string, mixed> $mtlsAuth only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/mtls-auths/{MTLSAuthId}', 'update-mtls-auth', OperationScope::Both)]
    public function update(string $id, MtlsAuthInput|array $mtlsAuth): MtlsAuth
    {
        return MtlsAuth::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $mtlsAuth,
        ));
    }

    /**
     * Create or replace an MTLS-auth credential by ID (operationId `upsert-mtls-auth`, PUT, body `MTLSAuth`).
     *
     * @param MtlsAuthInput|array<string, mixed> $mtlsAuth
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/mtls-auths/{MTLSAuthId}', 'upsert-mtls-auth', OperationScope::Both)]
    public function upsert(string $id, MtlsAuthInput|array $mtlsAuth): MtlsAuth
    {
        return MtlsAuth::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $mtlsAuth,
        ));
    }

    /**
     * Delete an MTLS-auth credential (operationId `delete-mtls-auth`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/mtls-auths/{MTLSAuthId}', 'delete-mtls-auth', OperationScope::Both)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $id));
    }
}
