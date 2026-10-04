<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\Route;
use Aybarsm\Kong\AdminApi\Models\RouteExpressionInput;
use Aybarsm\Kong\AdminApi\Models\RouteFactory;
use Aybarsm\Kong\AdminApi\Models\RouteJsonInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\Nested\RoutePlugins;
use Generator;

/**
 * Routes (spec tag "Routes"): `/routes` and `/routes/{RouteIdOrName}`.
 *
 * Responses are `RouteJson` or `RouteExpression` (see Route and RouteFactory).
 */
final readonly class Routes extends AbstractResource
{
    private const string SEGMENT = 'routes';

    /**
     * List one page of Routes (operationId `list-route`).
     *
     * @return Page<Route>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/routes', 'list-route', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), RouteFactory::fromArray(...), $options);
    }

    /**
     * Lazily iterate every Route across all pages (operationId `list-route`).
     *
     * @return Generator<int, Route>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/routes', 'list-route', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), RouteFactory::fromArray(...), $options);
    }

    /**
     * The page after $page, or null when $page is the last (operationId `list-route`).
     *
     * Repeats list() with the page's `offset`, keeping the same filters and workspace. Kong's own `next`
     * link is not followed (it drops filters on nested lists; spec-notes Q6).
     *
     * @param Page<Route> $page
     *
     * @return Page<Route>|null
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/routes', 'list-route', OperationScope::Both)]
    public function nextPage(Page $page, ?ListOptions $options = null): ?Page
    {
        return $page->offset === null ? null : $this->list(($options ?? new ListOptions())->withOffset($page->offset));
    }

    /**
     * Get a Route by ID or name (operationId `get-route`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_GET, '/routes/{RouteIdOrName}', 'get-route', OperationScope::Both)]
    public function get(string $idOrName): Route
    {
        return RouteFactory::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
        ));
    }

    /**
     * Create a Route (operationId `create-route`, body `Route`).
     *
     * @param RouteJsonInput|RouteExpressionInput|array<string, mixed> $route
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/routes', 'create-route', OperationScope::Both)]
    public function create(RouteJsonInput|RouteExpressionInput|array $route): Route
    {
        return RouteFactory::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $route,
        ));
    }

    /**
     * Update fields of a Route (operationId `update-route`, PATCH, body `Route`).
     *
     * @param RouteJsonInput|RouteExpressionInput|array<string, mixed> $route only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/routes/{RouteIdOrName}', 'update-route', OperationScope::Both)]
    public function update(string $idOrName, RouteJsonInput|RouteExpressionInput|array $route): Route
    {
        return RouteFactory::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
            body: $route,
        ));
    }

    /**
     * Create or replace a Route by ID or name (operationId `upsert-route`, PUT, body `Route`).
     *
     * @param RouteJsonInput|RouteExpressionInput|array<string, mixed> $route
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PUT, '/routes/{RouteIdOrName}', 'upsert-route', OperationScope::Both)]
    public function upsert(string $idOrName, RouteJsonInput|RouteExpressionInput|array $route): Route
    {
        return RouteFactory::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
            body: $route,
        ));
    }

    /**
     * Delete a Route (operationId `delete-route`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/routes/{RouteIdOrName}', 'delete-route', OperationScope::Both)]
    public function delete(string $idOrName): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $idOrName));
    }

    /**
     * Plugins scoped to one Route: `/routes/{RouteIdOrName}/plugins`.
     *
     * @throws InvalidArgumentException when $routeIdOrName is empty
     */
    public function plugins(string $routeIdOrName): RoutePlugins
    {
        if ($routeIdOrName === '') {
            throw new InvalidArgumentException('Route ID or name must not be empty.');
        }

        return new RoutePlugins($this->transport, [...$this->parent, self::SEGMENT, $routeIdOrName]);
    }
}
