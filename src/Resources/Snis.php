<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\Sni;
use Aybarsm\Kong\AdminApi\Models\SniInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Generator;

/**
 * SNIs (spec tag "SNIs"): `/snis` and `/snis/{SNIIdOrName}`.
 */
final readonly class Snis extends AbstractResource
{
    private const string SEGMENT = 'snis';

    /**
     * List one page of SNIs (operationId `list-sni`).
     *
     * @return Page<Sni>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/snis', 'list-sni', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), Sni::fromArray(...), $options);
    }

    /**
     * Lazily iterate every SNI across all pages (operationId `list-sni`).
     *
     * @return Generator<int, Sni>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/snis', 'list-sni', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), Sni::fromArray(...), $options);
    }

    /**
     * Get an SNI by ID or name (operationId `get-sni`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_GET, '/snis/{SNIIdOrName}', 'get-sni', OperationScope::Both)]
    public function get(string $idOrName): Sni
    {
        return Sni::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
        ));
    }

    /**
     * Create an SNI (operationId `create-sni`, body `SNI`).
     *
     * @param SniInput|array<string, mixed> $sni
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/snis', 'create-sni', OperationScope::Both)]
    public function create(SniInput|array $sni): Sni
    {
        return Sni::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $sni,
        ));
    }

    /**
     * Update fields of an SNI (operationId `update-sni`, PATCH, body `SNI`).
     *
     * @param SniInput|array<string, mixed> $sni only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/snis/{SNIIdOrName}', 'update-sni', OperationScope::Both)]
    public function update(string $idOrName, SniInput|array $sni): Sni
    {
        return Sni::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
            body: $sni,
        ));
    }

    /**
     * Create or replace an SNI by ID or name (operationId `upsert-sni`, PUT, body `SNI`).
     *
     * @param SniInput|array<string, mixed> $sni
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PUT, '/snis/{SNIIdOrName}', 'upsert-sni', OperationScope::Both)]
    public function upsert(string $idOrName, SniInput|array $sni): Sni
    {
        return Sni::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
            body: $sni,
        ));
    }

    /**
     * Delete an SNI (operationId `delete-sni`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/snis/{SNIIdOrName}', 'delete-sni', OperationScope::Both)]
    public function delete(string $idOrName): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $idOrName));
    }
}
