<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\Service;
use Aybarsm\Kong\AdminApi\Models\ServiceInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\Nested\ServiceDegraphqlRoutes;
use Aybarsm\Kong\AdminApi\Resources\Nested\ServiceGraphqlCostDecorations;
use Aybarsm\Kong\AdminApi\Resources\Nested\ServicePlugins;
use Aybarsm\Kong\AdminApi\Resources\Nested\ServiceRoutes;
use Generator;

/**
 * Services (spec tag "Services"): `/services` and `/services/{ServiceIdOrName}`.
 */
final readonly class Services extends AbstractResource
{
    private const string SEGMENT = 'services';

    /**
     * List one page of Services (operationId `list-service`).
     *
     * @return Page<Service>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/services', 'list-service', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), Service::fromArray(...), $options);
    }

    /**
     * Lazily iterate every Service across all pages (operationId `list-service`).
     *
     * @return Generator<int, Service>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/services', 'list-service', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), Service::fromArray(...), $options);
    }

    /**
     * The page after $page, or null when $page is the last (operationId `list-service`).
     *
     * Repeats list() with the page's `offset`, keeping the same filters and workspace. Kong's own `next`
     * link is not followed (it drops filters on nested lists; spec-notes Q6).
     *
     * @param Page<Service> $page
     *
     * @return Page<Service>|null
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/services', 'list-service', OperationScope::Both)]
    public function nextPage(Page $page, ?ListOptions $options = null): ?Page
    {
        return $page->offset === null ? null : $this->list(($options ?? new ListOptions())->withOffset($page->offset));
    }

    /**
     * Get a Service by ID or name (operationId `get-service`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_GET, '/services/{ServiceIdOrName}', 'get-service', OperationScope::Both)]
    public function get(string $idOrName): Service
    {
        return Service::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
        ));
    }

    /**
     * Create a Service (operationId `create-service`).
     *
     * @param ServiceInput|array<string, mixed> $service
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/services', 'create-service', OperationScope::Both)]
    public function create(ServiceInput|array $service): Service
    {
        return Service::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $service,
        ));
    }

    /**
     * Update fields of a Service (operationId `update-service`, PATCH).
     *
     * @param ServiceInput|array<string, mixed> $service only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/services/{ServiceIdOrName}', 'update-service', OperationScope::Both)]
    public function update(string $idOrName, ServiceInput|array $service): Service
    {
        return Service::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
            body: $service,
        ));
    }

    /**
     * Create or replace a Service by ID or name (operationId `upsert-service`, PUT).
     *
     * @param ServiceInput|array<string, mixed> $service
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PUT, '/services/{ServiceIdOrName}', 'upsert-service', OperationScope::Both)]
    public function upsert(string $idOrName, ServiceInput|array $service): Service
    {
        return Service::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
            body: $service,
        ));
    }

    /**
     * Delete a Service (operationId `delete-service`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/services/{ServiceIdOrName}', 'delete-service', OperationScope::Both)]
    public function delete(string $idOrName): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $idOrName));
    }

    /**
     * Routes nested under one Service: `/services/{ServiceIdOrName}/routes`.
     *
     * @throws InvalidArgumentException when $serviceIdOrName is empty
     */
    public function routes(string $serviceIdOrName): ServiceRoutes
    {
        if ($serviceIdOrName === '') {
            throw new InvalidArgumentException('Service ID or name must not be empty.');
        }

        return new ServiceRoutes($this->transport, [...$this->parent, self::SEGMENT, $serviceIdOrName]);
    }

    /**
     * Plugins scoped to one Service: `/services/{ServiceIdOrName}/plugins`.
     *
     * @throws InvalidArgumentException when $serviceIdOrName is empty
     */
    public function plugins(string $serviceIdOrName): ServicePlugins
    {
        if ($serviceIdOrName === '') {
            throw new InvalidArgumentException('Service ID or name must not be empty.');
        }

        return new ServicePlugins($this->transport, [...$this->parent, self::SEGMENT, $serviceIdOrName]);
    }

    /**
     * DeGraphQL routes of one Service: `/services/{ServiceIdOrName}/degraphql/routes`.
     *
     * @throws InvalidArgumentException when $serviceIdOrName is empty
     */
    public function degraphqlRoutes(string $serviceIdOrName): ServiceDegraphqlRoutes
    {
        if ($serviceIdOrName === '') {
            throw new InvalidArgumentException('Service ID or name must not be empty.');
        }

        return new ServiceDegraphqlRoutes($this->transport, [...$this->parent, self::SEGMENT, $serviceIdOrName]);
    }

    /**
     * GraphQL cost decorations of one Service: `/services/{ServiceIdOrName}/graphql-rate-limiting-advanced/costs`.
     *
     * @throws InvalidArgumentException when $serviceIdOrName is empty
     */
    public function graphqlCostDecorations(string $serviceIdOrName): ServiceGraphqlCostDecorations
    {
        if ($serviceIdOrName === '') {
            throw new InvalidArgumentException('Service ID or name must not be empty.');
        }

        return new ServiceGraphqlCostDecorations($this->transport, [...$this->parent, self::SEGMENT, $serviceIdOrName]);
    }
}
