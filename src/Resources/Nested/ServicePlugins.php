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
 * Plugins nested under one Service: `/services/{ServiceIdOrName}/plugins` and `/services/{ServiceIdOrName}/plugins/{PluginId}`.
 *
 * Obtain it with `$client->services()->plugins($serviceIdOrName)`.
 */
final readonly class ServicePlugins extends AbstractResource
{
    private const string SEGMENT = 'plugins';

    /**
     * List one page of Plugins (operationId `list-plugin-with-service`).
     *
     * @return Page<Plugin>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/services/{ServiceIdOrName}/plugins', 'list-plugin-with-service', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), Plugin::fromArray(...), $options);
    }

    /**
     * Lazily iterate every Plugin across all pages (operationId `list-plugin-with-service`).
     *
     * @return Generator<int, Plugin>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/services/{ServiceIdOrName}/plugins', 'list-plugin-with-service', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), Plugin::fromArray(...), $options);
    }

    /**
     * Get a Plugin by ID (operationId `get-plugin-with-service`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/services/{ServiceIdOrName}/plugins/{PluginId}', 'get-plugin-with-service', OperationScope::Both)]
    public function get(string $id): Plugin
    {
        return Plugin::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
        ));
    }

    /**
     * Create a Plugin (operationId `create-plugin-with-service`, body `PluginWithoutParents`).
     *
     * @param PluginInput|array<string, mixed> $plugin
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/services/{ServiceIdOrName}/plugins', 'create-plugin-with-service', OperationScope::Both)]
    public function create(PluginInput|array $plugin): Plugin
    {
        return Plugin::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $plugin,
        ));
    }

    /**
     * Update fields of a Plugin (operationId `update-plugin-with-service`, PATCH, body `Plugin`).
     *
     * @param PluginInput|array<string, mixed> $plugin only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/services/{ServiceIdOrName}/plugins/{PluginId}', 'update-plugin-with-service', OperationScope::Both)]
    public function update(string $id, PluginInput|array $plugin): Plugin
    {
        return Plugin::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $plugin,
        ));
    }

    /**
     * Create or replace a Plugin by ID (operationId `upsert-plugin-with-service`, PUT, body `PluginWithoutParents`).
     *
     * @param PluginInput|array<string, mixed> $plugin
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/services/{ServiceIdOrName}/plugins/{PluginId}', 'upsert-plugin-with-service', OperationScope::Both)]
    public function upsert(string $id, PluginInput|array $plugin): Plugin
    {
        return Plugin::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $plugin,
        ));
    }

    /**
     * Delete a Plugin (operationId `delete-plugin-with-service`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/services/{ServiceIdOrName}/plugins/{PluginId}', 'delete-plugin-with-service', OperationScope::Both)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $id));
    }
}
