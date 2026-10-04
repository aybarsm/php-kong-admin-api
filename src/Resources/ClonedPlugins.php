<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\ClonedPlugin;
use Aybarsm\Kong\AdminApi\Models\ClonedPluginInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Generator;

/**
 * Cloned Plugins (spec tag "Cloned Plugins"): `/cloned-plugins` and `/cloned-plugins/{ClonedPluginIdOrName}`.
 */
final readonly class ClonedPlugins extends AbstractResource
{
    private const string SEGMENT = 'cloned-plugins';

    /**
     * List one page of Cloned Plugins (operationId `list-cloned-plugin`).
     *
     * @return Page<ClonedPlugin>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/cloned-plugins', 'list-cloned-plugin', OperationScope::GlobalOnly)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::GlobalOnly, self::SEGMENT), ClonedPlugin::fromArray(...), $options);
    }

    /**
     * Lazily iterate every Cloned Plugin across all pages (operationId `list-cloned-plugin`).
     *
     * @return Generator<int, ClonedPlugin>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/cloned-plugins', 'list-cloned-plugin', OperationScope::GlobalOnly)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::GlobalOnly, self::SEGMENT), ClonedPlugin::fromArray(...), $options);
    }

    /**
     * Get a Cloned Plugin by ID or name (operationId `get-cloned-plugin`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_GET, '/cloned-plugins/{ClonedPluginIdOrName}', 'get-cloned-plugin', OperationScope::GlobalOnly)]
    public function get(string $idOrName): ClonedPlugin
    {
        return ClonedPlugin::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $idOrName),
        ));
    }

    /**
     * Create a Cloned Plugin (operationId `create-cloned-plugin`, body `ClonedPlugin`).
     *
     * @param ClonedPluginInput|array<string, mixed> $clonedPlugin
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/cloned-plugins', 'create-cloned-plugin', OperationScope::GlobalOnly)]
    public function create(ClonedPluginInput|array $clonedPlugin): ClonedPlugin
    {
        return ClonedPlugin::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT),
            body: $clonedPlugin,
        ));
    }

    /**
     * Update fields of a Cloned Plugin (operationId `update-cloned-plugin`, PATCH, body `ClonedPlugin`).
     *
     * @param ClonedPluginInput|array<string, mixed> $clonedPlugin only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/cloned-plugins/{ClonedPluginIdOrName}', 'update-cloned-plugin', OperationScope::GlobalOnly)]
    public function update(string $idOrName, ClonedPluginInput|array $clonedPlugin): ClonedPlugin
    {
        return ClonedPlugin::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $idOrName),
            body: $clonedPlugin,
        ));
    }

    /**
     * Create or replace a Cloned Plugin by ID or name (operationId `upsert-cloned-plugin`, PUT, body `ClonedPlugin`).
     *
     * @param ClonedPluginInput|array<string, mixed> $clonedPlugin
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PUT, '/cloned-plugins/{ClonedPluginIdOrName}', 'upsert-cloned-plugin', OperationScope::GlobalOnly)]
    public function upsert(string $idOrName, ClonedPluginInput|array $clonedPlugin): ClonedPlugin
    {
        return ClonedPlugin::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $idOrName),
            body: $clonedPlugin,
        ));
    }

    /**
     * Delete a Cloned Plugin (operationId `delete-cloned-plugin`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/cloned-plugins/{ClonedPluginIdOrName}', 'delete-cloned-plugin', OperationScope::GlobalOnly)]
    public function delete(string $idOrName): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::GlobalOnly, self::SEGMENT, $idOrName));
    }
}
