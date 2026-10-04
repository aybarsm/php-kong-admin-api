<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\Vault;
use Aybarsm\Kong\AdminApi\Models\VaultInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Generator;

/**
 * Vaults (spec tag "Vaults"): `/vaults` and `/vaults/{VaultIdOrPrefix}`.
 */
final readonly class Vaults extends AbstractResource
{
    private const string SEGMENT = 'vaults';

    /**
     * List one page of Vaults (operationId `list-vault`).
     *
     * @return Page<Vault>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/vaults', 'list-vault', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), Vault::fromArray(...), $options);
    }

    /**
     * Lazily iterate every Vault across all pages (operationId `list-vault`).
     *
     * @return Generator<int, Vault>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/vaults', 'list-vault', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), Vault::fromArray(...), $options);
    }

    /**
     * Get a Vault by ID or prefix (operationId `get-vault`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrPrefix is empty
     */
    #[Operation(Transport::METHOD_GET, '/vaults/{VaultIdOrPrefix}', 'get-vault', OperationScope::Both)]
    public function get(string $idOrPrefix): Vault
    {
        return Vault::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrPrefix),
        ));
    }

    /**
     * Create a Vault (operationId `create-vault`, body `Vault`).
     *
     * @param VaultInput|array<string, mixed> $vault
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/vaults', 'create-vault', OperationScope::Both)]
    public function create(VaultInput|array $vault): Vault
    {
        return Vault::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $vault,
        ));
    }

    /**
     * Update fields of a Vault (operationId `update-vault`, PATCH, body `Vault`).
     *
     * @param VaultInput|array<string, mixed> $vault only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrPrefix is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/vaults/{VaultIdOrPrefix}', 'update-vault', OperationScope::Both)]
    public function update(string $idOrPrefix, VaultInput|array $vault): Vault
    {
        return Vault::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrPrefix),
            body: $vault,
        ));
    }

    /**
     * Create or replace a Vault by ID or prefix (operationId `upsert-vault`, PUT, body `Vault`).
     *
     * @param VaultInput|array<string, mixed> $vault
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrPrefix is empty
     */
    #[Operation(Transport::METHOD_PUT, '/vaults/{VaultIdOrPrefix}', 'upsert-vault', OperationScope::Both)]
    public function upsert(string $idOrPrefix, VaultInput|array $vault): Vault
    {
        return Vault::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrPrefix),
            body: $vault,
        ));
    }

    /**
     * Delete a Vault (operationId `delete-vault`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrPrefix is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/vaults/{VaultIdOrPrefix}', 'delete-vault', OperationScope::Both)]
    public function delete(string $idOrPrefix): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $idOrPrefix));
    }
}
