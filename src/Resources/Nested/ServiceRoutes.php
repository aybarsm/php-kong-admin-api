<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources\Nested;

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
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;
use Generator;

/**
 * Routes of one Service: `/services/{ServiceIdOrName}/routes` and `/services/{ServiceIdOrName}/routes/{RouteIdOrName}`.
 *
 * Obtain it with `$client->services()->routes($serviceIdOrName)`.
 *
 * Responses are `RouteJson` or `RouteExpression` (see Route and RouteFactory).
 */
final readonly class ServiceRoutes extends AbstractResource
{
    private const string SEGMENT = 'routes';

    /**
     * List one page of Routes (operationId `list-route-with-service`).
     *
     * @return Page<Route>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/services/{ServiceIdOrName}/routes', 'list-route-with-service', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), RouteFactory::fromArray(...), $options);
    }

    /**
     * Lazily iterate every Route across all pages (operationId `list-route-with-service`).
     *
     * @return Generator<int, Route>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/services/{ServiceIdOrName}/routes', 'list-route-with-service', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), RouteFactory::fromArray(...), $options);
    }

    /**
     * Get a Route by ID or name (operationId `get-route-with-service`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_GET, '/services/{ServiceIdOrName}/routes/{RouteIdOrName}', 'get-route-with-service', OperationScope::Both)]
    public function get(string $idOrName): Route
    {
        return RouteFactory::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
        ));
    }

    /**
     * Create a Route (operationId `create-route-with-service`, body `RouteWithoutParents`).
     *
     * @param RouteJsonInput|RouteExpressionInput|array<string, mixed> $route
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/services/{ServiceIdOrName}/routes', 'create-route-with-service', OperationScope::Both)]
    public function create(RouteJsonInput|RouteExpressionInput|array $route): Route
    {
        return RouteFactory::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $route,
        ));
    }

    /**
     * Update fields of a Route (operationId `update-route-with-service`, PATCH, body `Route`).
     *
     * @param RouteJsonInput|RouteExpressionInput|array<string, mixed> $route only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/services/{ServiceIdOrName}/routes/{RouteIdOrName}', 'update-route-with-service', OperationScope::Both)]
    public function update(string $idOrName, RouteJsonInput|RouteExpressionInput|array $route): Route
    {
        return RouteFactory::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
            body: $route,
        ));
    }

    /**
     * Create or replace a Route by ID or name (operationId `upsert-route-with-service`, PUT, body `RouteWithoutParents`).
     *
     * @param RouteJsonInput|RouteExpressionInput|array<string, mixed> $route
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PUT, '/services/{ServiceIdOrName}/routes/{RouteIdOrName}', 'upsert-route-with-service', OperationScope::Both)]
    public function upsert(string $idOrName, RouteJsonInput|RouteExpressionInput|array $route): Route
    {
        return RouteFactory::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
            body: $route,
        ));
    }

    /**
     * Delete a Route (operationId `delete-route-with-service`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/services/{ServiceIdOrName}/routes/{RouteIdOrName}', 'delete-route-with-service', OperationScope::Both)]
    public function delete(string $idOrName): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $idOrName));
    }
}
