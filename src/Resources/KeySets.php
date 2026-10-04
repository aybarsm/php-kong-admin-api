<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\KeySet;
use Aybarsm\Kong\AdminApi\Models\KeySetInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\Nested\KeySetKeys;
use Generator;

/**
 * Key Sets (spec tag "KeySets"): `/key-sets` and `/key-sets/{KeySetIdOrName}`.
 */
final readonly class KeySets extends AbstractResource
{
    private const string SEGMENT = 'key-sets';

    /**
     * List one page of Key Sets (operationId `list-key-set`).
     *
     * @return Page<KeySet>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/key-sets', 'list-key-set', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), KeySet::fromArray(...), $options);
    }

    /**
     * Lazily iterate every Key Set across all pages (operationId `list-key-set`).
     *
     * @return Generator<int, KeySet>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/key-sets', 'list-key-set', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), KeySet::fromArray(...), $options);
    }

    /**
     * The page after $page, or null when $page is the last (operationId `list-key-set`).
     *
     * Repeats list() with the page's `offset`, keeping the same filters and workspace. Kong's own `next`
     * link is not followed (it drops filters on nested lists; spec-notes Q6).
     *
     * @param Page<KeySet> $page
     *
     * @return Page<KeySet>|null
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/key-sets', 'list-key-set', OperationScope::Both)]
    public function nextPage(Page $page, ?ListOptions $options = null): ?Page
    {
        return $page->offset === null ? null : $this->list(($options ?? new ListOptions())->withOffset($page->offset));
    }

    /**
     * Get a Key Set by ID or name (operationId `get-key-set`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_GET, '/key-sets/{KeySetIdOrName}', 'get-key-set', OperationScope::Both)]
    public function get(string $idOrName): KeySet
    {
        return KeySet::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
        ));
    }

    /**
     * Create a Key Set (operationId `create-key-set`, body `KeySet`).
     *
     * @param KeySetInput|array<string, mixed> $keySet
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/key-sets', 'create-key-set', OperationScope::Both)]
    public function create(KeySetInput|array $keySet): KeySet
    {
        return KeySet::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $keySet,
        ));
    }

    /**
     * Update fields of a Key Set (operationId `update-key-set`, PATCH, body `KeySet`).
     *
     * @param KeySetInput|array<string, mixed> $keySet only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/key-sets/{KeySetIdOrName}', 'update-key-set', OperationScope::Both)]
    public function update(string $idOrName, KeySetInput|array $keySet): KeySet
    {
        return KeySet::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
            body: $keySet,
        ));
    }

    /**
     * Create or replace a Key Set by ID or name (operationId `upsert-key-set`, PUT, body `KeySet`).
     *
     * @param KeySetInput|array<string, mixed> $keySet
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PUT, '/key-sets/{KeySetIdOrName}', 'upsert-key-set', OperationScope::Both)]
    public function upsert(string $idOrName, KeySetInput|array $keySet): KeySet
    {
        return KeySet::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
            body: $keySet,
        ));
    }

    /**
     * Delete a Key Set (operationId `delete-key-set`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/key-sets/{KeySetIdOrName}', 'delete-key-set', OperationScope::Both)]
    public function delete(string $idOrName): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $idOrName));
    }

    /**
     * Keys of one Key Set: `/key-sets/{KeySetIdOrName}/keys`.
     *
     * @throws InvalidArgumentException when $keySetIdOrName is empty
     */
    public function keys(string $keySetIdOrName): KeySetKeys
    {
        if ($keySetIdOrName === '') {
            throw new InvalidArgumentException('Key Set ID or name must not be empty.');
        }

        return new KeySetKeys($this->transport, [...$this->parent, self::SEGMENT, $keySetIdOrName]);
    }
}
