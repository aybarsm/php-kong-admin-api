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
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;
use Generator;

/**
 * Plugins nested under one Route: `/routes/{RouteIdOrName}/plugins` and `/routes/{RouteIdOrName}/plugins/{PluginId}`.
 *
 * Obtain it with `$client->routes()->plugins($routeIdOrName)`.
 */
final readonly class RoutePlugins extends AbstractResource
{
    private const string SEGMENT = 'plugins';

    /**
     * List one page of Plugins (operationId `list-plugin-with-route`).
     *
     * @return Page<Plugin>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/routes/{RouteIdOrName}/plugins', 'list-plugin-with-route', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), Plugin::fromArray(...), $options);
    }

    /**
     * Lazily iterate every Plugin across all pages (operationId `list-plugin-with-route`).
     *
     * @return Generator<int, Plugin>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/routes/{RouteIdOrName}/plugins', 'list-plugin-with-route', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), Plugin::fromArray(...), $options);
    }

    /**
     * Get a Plugin by ID (operationId `get-plugin-with-route`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/routes/{RouteIdOrName}/plugins/{PluginId}', 'get-plugin-with-route', OperationScope::Both)]
    public function get(string $id): Plugin
    {
        return Plugin::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
        ));
    }

    /**
     * Create a Plugin (operationId `create-plugin-with-route`, body `PluginWithoutParents`).
     *
     * @param PluginInput|array<string, mixed> $plugin
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/routes/{RouteIdOrName}/plugins', 'create-plugin-with-route', OperationScope::Both)]
    public function create(PluginInput|array $plugin): Plugin
    {
        return Plugin::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $plugin,
        ));
    }

    /**
     * Update fields of a Plugin (operationId `update-plugin-with-route`, PATCH, body `Plugin`).
     *
     * @param PluginInput|array<string, mixed> $plugin only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/routes/{RouteIdOrName}/plugins/{PluginId}', 'update-plugin-with-route', OperationScope::Both)]
    public function update(string $id, PluginInput|array $plugin): Plugin
    {
        return Plugin::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $plugin,
        ));
    }

    /**
     * Create or replace a Plugin by ID (operationId `upsert-plugin-with-route`, PUT, body `PluginWithoutParents`).
     *
     * @param PluginInput|array<string, mixed> $plugin
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/routes/{RouteIdOrName}/plugins/{PluginId}', 'upsert-plugin-with-route', OperationScope::Both)]
    public function upsert(string $id, PluginInput|array $plugin): Plugin
    {
        return Plugin::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $plugin,
        ));
    }

    /**
     * Delete a Plugin (operationId `delete-plugin-with-route`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/routes/{RouteIdOrName}/plugins/{PluginId}', 'delete-plugin-with-route', OperationScope::Both)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $id));
    }
}
