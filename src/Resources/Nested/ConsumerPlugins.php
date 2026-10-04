<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources\Nested;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Models\PluginInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Plugins\TypedPluginInput;
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;
use Generator;

/**
 * Plugins nested under one Consumer: `/consumers/{ConsumerIdForNestedEntities}/plugins` and `/consumers/{ConsumerIdForNestedEntities}/plugins/{PluginId}`.
 *
 * Obtain it with `$client->consumers()->plugins($consumerId)`.
 */
final readonly class ConsumerPlugins extends AbstractResource
{
    private const string SEGMENT = 'plugins';

    /**
     * List one page of Plugins (operationId `list-plugin-with-consumer`).
     *
     * @return Page<Plugin>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/consumers/{ConsumerIdForNestedEntities}/plugins', 'list-plugin-with-consumer', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), Plugin::fromArray(...), $options);
    }

    /**
     * Lazily iterate every Plugin across all pages (operationId `list-plugin-with-consumer`).
     *
     * @return Generator<int, Plugin>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/consumers/{ConsumerIdForNestedEntities}/plugins', 'list-plugin-with-consumer', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), Plugin::fromArray(...), $options);
    }

    /**
     * The page after $page, or null when $page is the last (operationId `list-plugin-with-consumer`).
     *
     * Repeats list() with the page's `offset`, keeping the same filters and workspace. Kong's own `next`
     * link is not followed (it drops filters on nested lists; spec-notes Q6).
     *
     * @param Page<Plugin> $page
     *
     * @return Page<Plugin>|null
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/consumers/{ConsumerIdForNestedEntities}/plugins', 'list-plugin-with-consumer', OperationScope::Both)]
    public function nextPage(Page $page, ?ListOptions $options = null): ?Page
    {
        return $page->offset === null ? null : $this->list(($options ?? new ListOptions())->withOffset($page->offset));
    }

    /**
     * Get a Plugin by ID (operationId `get-plugin-with-consumer`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/consumers/{ConsumerIdForNestedEntities}/plugins/{PluginId}', 'get-plugin-with-consumer', OperationScope::Both)]
    public function get(string $id): Plugin
    {
        return Plugin::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
        ));
    }

    /**
     * Create a Plugin (operationId `create-plugin-with-consumer`, body `PluginWithoutParents`).
     *
     * @param PluginInput|TypedPluginInput|array<string, mixed> $plugin
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/consumers/{ConsumerIdForNestedEntities}/plugins', 'create-plugin-with-consumer', OperationScope::Both)]
    public function create(PluginInput|TypedPluginInput|array $plugin): Plugin
    {
        return Plugin::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $plugin,
        ));
    }

    /**
     * Update fields of a Plugin (operationId `update-plugin-with-consumer`, PATCH, body `Plugin`).
     *
     * @param PluginInput|TypedPluginInput|array<string, mixed> $plugin only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/consumers/{ConsumerIdForNestedEntities}/plugins/{PluginId}', 'update-plugin-with-consumer', OperationScope::Both)]
    public function update(string $id, PluginInput|TypedPluginInput|array $plugin): Plugin
    {
        return Plugin::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $plugin,
        ));
    }

    /**
     * Create or replace a Plugin by ID (operationId `upsert-plugin-with-consumer`, PUT, body `PluginWithoutParents`).
     *
     * @param PluginInput|TypedPluginInput|array<string, mixed> $plugin
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/consumers/{ConsumerIdForNestedEntities}/plugins/{PluginId}', 'upsert-plugin-with-consumer', OperationScope::Both)]
    public function upsert(string $id, PluginInput|TypedPluginInput|array $plugin): Plugin
    {
        return Plugin::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $plugin,
        ));
    }

    /**
     * Delete a Plugin (operationId `delete-plugin-with-consumer`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/consumers/{ConsumerIdForNestedEntities}/plugins/{PluginId}', 'delete-plugin-with-consumer', OperationScope::Both)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $id));
    }
}
