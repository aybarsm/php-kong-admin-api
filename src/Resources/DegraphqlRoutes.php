<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\DegraphqlRoute;
use Aybarsm\Kong\AdminApi\Models\DegraphqlRouteInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Generator;

/**
 * DeGraphQL routes (spec tag "Degraphql_routes"): `/degraphql_routes` and `/degraphql_routes/{Degraphql_routeIdOrName}`.
 */
final readonly class DegraphqlRoutes extends AbstractResource
{
    private const string SEGMENT = 'degraphql_routes';

    /**
     * List one page of DeGraphQL routes (operationId `list-degraphql_route`).
     *
     * @return Page<DegraphqlRoute>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/degraphql_routes', 'list-degraphql_route', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), DegraphqlRoute::fromArray(...), $options);
    }

    /**
     * Lazily iterate every DeGraphQL route across all pages (operationId `list-degraphql_route`).
     *
     * @return Generator<int, DegraphqlRoute>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/degraphql_routes', 'list-degraphql_route', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), DegraphqlRoute::fromArray(...), $options);
    }

    /**
     * The page after $page, or null when $page is the last (operationId `list-degraphql_route`).
     *
     * Repeats list() with the page's `offset`, keeping the same filters and workspace. Kong's own `next`
     * link is not followed (it drops filters on nested lists; spec-notes Q6).
     *
     * @param Page<DegraphqlRoute> $page
     *
     * @return Page<DegraphqlRoute>|null
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/degraphql_routes', 'list-degraphql_route', OperationScope::Both)]
    public function nextPage(Page $page, ?ListOptions $options = null): ?Page
    {
        return $page->offset === null ? null : $this->list(($options ?? new ListOptions())->withOffset($page->offset));
    }

    /**
     * Get a DeGraphQL route by ID or name (operationId `get-degraphql_route`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_GET, '/degraphql_routes/{Degraphql_routeIdOrName}', 'get-degraphql_route', OperationScope::Both)]
    public function get(string $idOrName): DegraphqlRoute
    {
        return DegraphqlRoute::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
        ));
    }

    /**
     * Create a DeGraphQL route (operationId `create-degraphql_route`, body `Degraphql_route`).
     *
     * @param DegraphqlRouteInput|array<string, mixed> $degraphqlRoute
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/degraphql_routes', 'create-degraphql_route', OperationScope::Both)]
    public function create(DegraphqlRouteInput|array $degraphqlRoute): DegraphqlRoute
    {
        return DegraphqlRoute::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $degraphqlRoute,
        ));
    }

    /**
     * Update fields of a DeGraphQL route (operationId `update-degraphql_route`, PATCH, body `Degraphql_route`).
     *
     * @param DegraphqlRouteInput|array<string, mixed> $degraphqlRoute only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/degraphql_routes/{Degraphql_routeIdOrName}', 'update-degraphql_route', OperationScope::Both)]
    public function update(string $idOrName, DegraphqlRouteInput|array $degraphqlRoute): DegraphqlRoute
    {
        return DegraphqlRoute::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
            body: $degraphqlRoute,
        ));
    }

    /**
     * Create or replace a DeGraphQL route by ID or name (operationId `upsert-degraphql_route`, PUT, body `Degraphql_route`).
     *
     * @param DegraphqlRouteInput|array<string, mixed> $degraphqlRoute
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PUT, '/degraphql_routes/{Degraphql_routeIdOrName}', 'upsert-degraphql_route', OperationScope::Both)]
    public function upsert(string $idOrName, DegraphqlRouteInput|array $degraphqlRoute): DegraphqlRoute
    {
        return DegraphqlRoute::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
            body: $degraphqlRoute,
        ));
    }

    /**
     * Delete a DeGraphQL route (operationId `delete-degraphql_route`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/degraphql_routes/{Degraphql_routeIdOrName}', 'delete-degraphql_route', OperationScope::Both)]
    public function delete(string $idOrName): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $idOrName));
    }
}
