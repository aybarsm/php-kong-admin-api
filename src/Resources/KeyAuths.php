<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\KeyAuth;
use Aybarsm\Kong\AdminApi\Models\KeyAuthInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Generator;

/**
 * API keys (spec tag "API-keys"): `/key-auths` and `/key-auths/{KeyAuthId}`.
 */
final readonly class KeyAuths extends AbstractResource
{
    private const string SEGMENT = 'key-auths';

    /**
     * List one page of API keys (operationId `list-key-auth`).
     *
     * @return Page<KeyAuth>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/key-auths', 'list-key-auth', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), KeyAuth::fromArray(...), $options);
    }

    /**
     * Lazily iterate every API key across all pages (operationId `list-key-auth`).
     *
     * @return Generator<int, KeyAuth>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/key-auths', 'list-key-auth', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), KeyAuth::fromArray(...), $options);
    }

    /**
     * The page after $page, or null when $page is the last (operationId `list-key-auth`).
     *
     * Repeats list() with the page's `offset`, keeping the same filters and workspace. Kong's own `next`
     * link is not followed (it drops filters on nested lists; spec-notes Q6).
     *
     * @param Page<KeyAuth> $page
     *
     * @return Page<KeyAuth>|null
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/key-auths', 'list-key-auth', OperationScope::Both)]
    public function nextPage(Page $page, ?ListOptions $options = null): ?Page
    {
        return $page->offset === null ? null : $this->list(($options ?? new ListOptions())->withOffset($page->offset));
    }

    /**
     * Get an API key by ID (operationId `get-key-auth`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/key-auths/{KeyAuthId}', 'get-key-auth', OperationScope::Both)]
    public function get(string $id): KeyAuth
    {
        return KeyAuth::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
        ));
    }

    /**
     * Create an API key (operationId `create-key-auth`, body `KeyAuth`).
     *
     * @param KeyAuthInput|array<string, mixed> $keyAuth
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/key-auths', 'create-key-auth', OperationScope::Both)]
    public function create(KeyAuthInput|array $keyAuth): KeyAuth
    {
        return KeyAuth::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $keyAuth,
        ));
    }

    /**
     * Update fields of an API key (operationId `update-key-auth`, PATCH, body `KeyAuth`).
     *
     * @param KeyAuthInput|array<string, mixed> $keyAuth only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/key-auths/{KeyAuthId}', 'update-key-auth', OperationScope::Both)]
    public function update(string $id, KeyAuthInput|array $keyAuth): KeyAuth
    {
        return KeyAuth::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $keyAuth,
        ));
    }

    /**
     * Create or replace an API key by ID (operationId `upsert-key-auth`, PUT, body `KeyAuth`).
     *
     * @param KeyAuthInput|array<string, mixed> $keyAuth
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/key-auths/{KeyAuthId}', 'upsert-key-auth', OperationScope::Both)]
    public function upsert(string $id, KeyAuthInput|array $keyAuth): KeyAuth
    {
        return KeyAuth::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $keyAuth,
        ));
    }

    /**
     * Delete an API key (operationId `delete-key-auth`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/key-auths/{KeyAuthId}', 'delete-key-auth', OperationScope::Both)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $id));
    }
}
