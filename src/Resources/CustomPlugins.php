<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\CustomPlugin;
use Aybarsm\Kong\AdminApi\Models\CustomPluginInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Generator;

/**
 * Custom Plugins (spec tag "CustomPlugins"): `/custom-plugins` and `/custom-plugins/{CustomPluginIdOrName}`.
 */
final readonly class CustomPlugins extends AbstractResource
{
    private const string SEGMENT = 'custom-plugins';

    /**
     * List one page of Custom Plugins (operationId `list-custom-plugin`).
     *
     * @return Page<CustomPlugin>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/custom-plugins', 'list-custom-plugin', OperationScope::GlobalOnly)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::GlobalOnly, self::SEGMENT), CustomPlugin::fromArray(...), $options);
    }

    /**
     * Lazily iterate every Custom Plugin across all pages (operationId `list-custom-plugin`).
     *
     * @return Generator<int, CustomPlugin>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/custom-plugins', 'list-custom-plugin', OperationScope::GlobalOnly)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::GlobalOnly, self::SEGMENT), CustomPlugin::fromArray(...), $options);
    }

    /**
     * Get a Custom Plugin by ID or name (operationId `get-custom-plugin`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_GET, '/custom-plugins/{CustomPluginIdOrName}', 'get-custom-plugin', OperationScope::GlobalOnly)]
    public function get(string $idOrName): CustomPlugin
    {
        return CustomPlugin::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $idOrName),
        ));
    }

    /**
     * Create a Custom Plugin (operationId `create-custom-plugin`, body `CustomPlugin`).
     *
     * @param CustomPluginInput|array<string, mixed> $customPlugin
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/custom-plugins', 'create-custom-plugin', OperationScope::GlobalOnly)]
    public function create(CustomPluginInput|array $customPlugin): CustomPlugin
    {
        return CustomPlugin::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT),
            body: $customPlugin,
        ));
    }

    /**
     * Update fields of a Custom Plugin (operationId `update-custom-plugin`, PATCH, body `CustomPlugin`).
     *
     * @param CustomPluginInput|array<string, mixed> $customPlugin only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/custom-plugins/{CustomPluginIdOrName}', 'update-custom-plugin', OperationScope::GlobalOnly)]
    public function update(string $idOrName, CustomPluginInput|array $customPlugin): CustomPlugin
    {
        return CustomPlugin::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $idOrName),
            body: $customPlugin,
        ));
    }

    /**
     * Create or replace a Custom Plugin by ID or name (operationId `upsert-custom-plugin`, PUT, body `CustomPlugin`).
     *
     * @param CustomPluginInput|array<string, mixed> $customPlugin
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PUT, '/custom-plugins/{CustomPluginIdOrName}', 'upsert-custom-plugin', OperationScope::GlobalOnly)]
    public function upsert(string $idOrName, CustomPluginInput|array $customPlugin): CustomPlugin
    {
        return CustomPlugin::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $idOrName),
            body: $customPlugin,
        ));
    }

    /**
     * Delete a Custom Plugin (operationId `delete-custom-plugin`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/custom-plugins/{CustomPluginIdOrName}', 'delete-custom-plugin', OperationScope::GlobalOnly)]
    public function delete(string $idOrName): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::GlobalOnly, self::SEGMENT, $idOrName));
    }
}
