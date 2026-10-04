<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources\Nested;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\Key;
use Aybarsm\Kong\AdminApi\Models\KeyInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;
use Generator;

/**
 * Keys nested under one Key Set: `/key-sets/{KeySetIdOrName}/keys` and `/key-sets/{KeySetIdOrName}/keys/{KeyIdOrName}`.
 *
 * Obtain it with `$client->keySets()->keys($keySetIdOrName)`.
 */
final readonly class KeySetKeys extends AbstractResource
{
    private const string SEGMENT = 'keys';

    /**
     * List one page of Keys (operationId `list-key-with-key-set`).
     *
     * @return Page<Key>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/key-sets/{KeySetIdOrName}/keys', 'list-key-with-key-set', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), Key::fromArray(...), $options);
    }

    /**
     * Lazily iterate every Key across all pages (operationId `list-key-with-key-set`).
     *
     * @return Generator<int, Key>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/key-sets/{KeySetIdOrName}/keys', 'list-key-with-key-set', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), Key::fromArray(...), $options);
    }

    /**
     * The page after $page, or null when $page is the last (operationId `list-key-with-key-set`).
     *
     * Repeats list() with the page's `offset`, keeping the same filters and workspace. Kong's own `next`
     * link is not followed (it drops filters on nested lists; spec-notes Q6).
     *
     * @param Page<Key> $page
     *
     * @return Page<Key>|null
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/key-sets/{KeySetIdOrName}/keys', 'list-key-with-key-set', OperationScope::Both)]
    public function nextPage(Page $page, ?ListOptions $options = null): ?Page
    {
        return $page->offset === null ? null : $this->list(($options ?? new ListOptions())->withOffset($page->offset));
    }

    /**
     * Get a Key by ID or name (operationId `get-key-with-key-set`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_GET, '/key-sets/{KeySetIdOrName}/keys/{KeyIdOrName}', 'get-key-with-key-set', OperationScope::Both)]
    public function get(string $idOrName): Key
    {
        return Key::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
        ));
    }

    /**
     * Create a Key (operationId `create-key-with-key-set`, body `KeyWithoutParents`).
     *
     * @param KeyInput|array<string, mixed> $key
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/key-sets/{KeySetIdOrName}/keys', 'create-key-with-key-set', OperationScope::Both)]
    public function create(KeyInput|array $key): Key
    {
        return Key::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $key,
        ));
    }

    /**
     * Update fields of a Key (operationId `update-key-with-key-set`, PATCH, body `Key`).
     *
     * @param KeyInput|array<string, mixed> $key only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/key-sets/{KeySetIdOrName}/keys/{KeyIdOrName}', 'update-key-with-key-set', OperationScope::Both)]
    public function update(string $idOrName, KeyInput|array $key): Key
    {
        return Key::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
            body: $key,
        ));
    }

    /**
     * Create or replace a Key by ID or name (operationId `upsert-key-with-key-set`, PUT, body `KeyWithoutParents`).
     *
     * @param KeyInput|array<string, mixed> $key
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PUT, '/key-sets/{KeySetIdOrName}/keys/{KeyIdOrName}', 'upsert-key-with-key-set', OperationScope::Both)]
    public function upsert(string $idOrName, KeyInput|array $key): Key
    {
        return Key::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
            body: $key,
        ));
    }

    /**
     * Delete a Key (operationId `delete-key-with-key-set`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/key-sets/{KeySetIdOrName}/keys/{KeyIdOrName}', 'delete-key-with-key-set', OperationScope::Both)]
    public function delete(string $idOrName): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $idOrName));
    }
}
