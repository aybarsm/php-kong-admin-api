<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources\Nested;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\DegraphqlRoute;
use Aybarsm\Kong\AdminApi\Models\DegraphqlRouteInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;
use Generator;

/**
 * DeGraphQL routes nested under one Service: `/services/{ServiceIdOrName}/degraphql/routes` and `/services/{ServiceIdOrName}/degraphql/routes/{Degraphql_routeIdOrName}`.
 *
 * Obtain it with `$client->services()->degraphqlRoutes($serviceIdOrName)`.
 */
final readonly class ServiceDegraphqlRoutes extends AbstractResource
{
    /**
     * List one page of DeGraphQL routes (operationId `list-degraphql_route-with-service`).
     *
     * @return Page<DegraphqlRoute>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/services/{ServiceIdOrName}/degraphql/routes', 'list-degraphql_route-with-service', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, 'degraphql', 'routes'), DegraphqlRoute::fromArray(...), $options);
    }

    /**
     * Lazily iterate every DeGraphQL route across all pages (operationId `list-degraphql_route-with-service`).
     *
     * @return Generator<int, DegraphqlRoute>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/services/{ServiceIdOrName}/degraphql/routes', 'list-degraphql_route-with-service', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, 'degraphql', 'routes'), DegraphqlRoute::fromArray(...), $options);
    }

    /**
     * The page after $page, or null when $page is the last (operationId `list-degraphql_route-with-service`).
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
    #[Operation(Transport::METHOD_GET, '/services/{ServiceIdOrName}/degraphql/routes', 'list-degraphql_route-with-service', OperationScope::Both)]
    public function nextPage(Page $page, ?ListOptions $options = null): ?Page
    {
        return $page->offset === null ? null : $this->list(($options ?? new ListOptions())->withOffset($page->offset));
    }

    /**
     * Get a DeGraphQL route by ID or name (operationId `get-degraphql_route-with-service`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_GET, '/services/{ServiceIdOrName}/degraphql/routes/{Degraphql_routeIdOrName}', 'get-degraphql_route-with-service', OperationScope::Both)]
    public function get(string $idOrName): DegraphqlRoute
    {
        return DegraphqlRoute::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, 'degraphql', 'routes', $idOrName),
        ));
    }

    /**
     * Create a DeGraphQL route (operationId `create-degraphql_route-with-service`, body `Degraphql_routeWithoutParents`).
     *
     * @param DegraphqlRouteInput|array<string, mixed> $degraphqlRoute
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/services/{ServiceIdOrName}/degraphql/routes', 'create-degraphql_route-with-service', OperationScope::Both)]
    public function create(DegraphqlRouteInput|array $degraphqlRoute): DegraphqlRoute
    {
        return DegraphqlRoute::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, 'degraphql', 'routes'),
            body: $degraphqlRoute,
        ));
    }

    /**
     * Update fields of a DeGraphQL route (operationId `update-degraphql_route-with-service`, PATCH, body `Degraphql_route`).
     *
     * @param DegraphqlRouteInput|array<string, mixed> $degraphqlRoute only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/services/{ServiceIdOrName}/degraphql/routes/{Degraphql_routeIdOrName}', 'update-degraphql_route-with-service', OperationScope::Both)]
    public function update(string $idOrName, DegraphqlRouteInput|array $degraphqlRoute): DegraphqlRoute
    {
        return DegraphqlRoute::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, 'degraphql', 'routes', $idOrName),
            body: $degraphqlRoute,
        ));
    }

    /**
     * Create or replace a DeGraphQL route by ID or name (operationId `upsert-degraphql_route-with-service`, PUT, body `Degraphql_routeWithoutParents`).
     *
     * @param DegraphqlRouteInput|array<string, mixed> $degraphqlRoute
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PUT, '/services/{ServiceIdOrName}/degraphql/routes/{Degraphql_routeIdOrName}', 'upsert-degraphql_route-with-service', OperationScope::Both)]
    public function upsert(string $idOrName, DegraphqlRouteInput|array $degraphqlRoute): DegraphqlRoute
    {
        return DegraphqlRoute::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, 'degraphql', 'routes', $idOrName),
            body: $degraphqlRoute,
        ));
    }

    /**
     * Delete a DeGraphQL route (operationId `delete-degraphql_route-with-service`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/services/{ServiceIdOrName}/degraphql/routes/{Degraphql_routeIdOrName}', 'delete-degraphql_route-with-service', OperationScope::Both)]
    public function delete(string $idOrName): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, 'degraphql', 'routes', $idOrName));
    }
}
