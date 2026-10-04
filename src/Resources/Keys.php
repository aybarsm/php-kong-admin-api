<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\Key;
use Aybarsm\Kong\AdminApi\Models\KeyInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Generator;

/**
 * Keys (spec tag "Keys"): `/keys` and `/keys/{KeyIdOrName}`.
 */
final readonly class Keys extends AbstractResource
{
    private const string SEGMENT = 'keys';

    /**
     * List one page of Keys (operationId `list-key`).
     *
     * @return Page<Key>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/keys', 'list-key', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), Key::fromArray(...), $options);
    }

    /**
     * Lazily iterate every Key across all pages (operationId `list-key`).
     *
     * @return Generator<int, Key>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/keys', 'list-key', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), Key::fromArray(...), $options);
    }

    /**
     * Get a Key by ID or name (operationId `get-key`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_GET, '/keys/{KeyIdOrName}', 'get-key', OperationScope::Both)]
    public function get(string $idOrName): Key
    {
        return Key::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
        ));
    }

    /**
     * Create a Key (operationId `create-key`, body `Key`).
     *
     * @param KeyInput|array<string, mixed> $key
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/keys', 'create-key', OperationScope::Both)]
    public function create(KeyInput|array $key): Key
    {
        return Key::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $key,
        ));
    }

    /**
     * Update fields of a Key (operationId `update-key`, PATCH, body `Key`).
     *
     * @param KeyInput|array<string, mixed> $key only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/keys/{KeyIdOrName}', 'update-key', OperationScope::Both)]
    public function update(string $idOrName, KeyInput|array $key): Key
    {
        return Key::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
            body: $key,
        ));
    }

    /**
     * Create or replace a Key by ID or name (operationId `upsert-key`, PUT, body `Key`).
     *
     * @param KeyInput|array<string, mixed> $key
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PUT, '/keys/{KeyIdOrName}', 'upsert-key', OperationScope::Both)]
    public function upsert(string $idOrName, KeyInput|array $key): Key
    {
        return Key::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
            body: $key,
        ));
    }

    /**
     * Delete a Key (operationId `delete-key`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/keys/{KeyIdOrName}', 'delete-key', OperationScope::Both)]
    public function delete(string $idOrName): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $idOrName));
    }
}
