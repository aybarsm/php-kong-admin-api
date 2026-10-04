<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources\Nested;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\GraphqlCostDecoration;
use Aybarsm\Kong\AdminApi\Models\GraphqlCostDecorationInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;
use Generator;

/**
 * GraphQL cost decorations nested under one Service: `/services/{ServiceIdOrName}/graphql-rate-limiting-advanced/costs` and `/services/{ServiceIdOrName}/graphql-rate-limiting-advanced/costs/{GraphQLCostDecorationId}`.
 *
 * Obtain it with `$client->services()->graphqlCostDecorations($serviceIdOrName)`.
 */
final readonly class ServiceGraphqlCostDecorations extends AbstractResource
{
    /**
     * List one page of GraphQL cost decorations (operationId `list-graphql-rate-limiting-advanced-cost-with-service`).
     *
     * @return Page<GraphqlCostDecoration>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/services/{ServiceIdOrName}/graphql-rate-limiting-advanced/costs', 'list-graphql-rate-limiting-advanced-cost-with-service', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, 'graphql-rate-limiting-advanced', 'costs'), GraphqlCostDecoration::fromArray(...), $options);
    }

    /**
     * Lazily iterate every GraphQL cost decoration across all pages (operationId `list-graphql-rate-limiting-advanced-cost-with-service`).
     *
     * @return Generator<int, GraphqlCostDecoration>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/services/{ServiceIdOrName}/graphql-rate-limiting-advanced/costs', 'list-graphql-rate-limiting-advanced-cost-with-service', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, 'graphql-rate-limiting-advanced', 'costs'), GraphqlCostDecoration::fromArray(...), $options);
    }

    /**
     * The page after $page, or null when $page is the last (operationId `list-graphql-rate-limiting-advanced-cost-with-service`).
     *
     * Repeats list() with the page's `offset`, keeping the same filters and workspace. Kong's own `next`
     * link is not followed (it drops filters on nested lists; spec-notes Q6).
     *
     * @param Page<GraphqlCostDecoration> $page
     *
     * @return Page<GraphqlCostDecoration>|null
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/services/{ServiceIdOrName}/graphql-rate-limiting-advanced/costs', 'list-graphql-rate-limiting-advanced-cost-with-service', OperationScope::Both)]
    public function nextPage(Page $page, ?ListOptions $options = null): ?Page
    {
        return $page->offset === null ? null : $this->list(($options ?? new ListOptions())->withOffset($page->offset));
    }

    /**
     * Get a GraphQL cost decoration by ID (operationId `get-graphql-rate-limiting-advanced-cost-with-service`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/services/{ServiceIdOrName}/graphql-rate-limiting-advanced/costs/{GraphQLCostDecorationId}', 'get-graphql-rate-limiting-advanced-cost-with-service', OperationScope::Both)]
    public function get(string $id): GraphqlCostDecoration
    {
        return GraphqlCostDecoration::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, 'graphql-rate-limiting-advanced', 'costs', $id),
        ));
    }

    /**
     * Create a GraphQL cost decoration (operationId `create-graphql-rate-limiting-advanced-cost-with-service`, body `GraphQLCostDecorationWithoutParents`).
     *
     * @param GraphqlCostDecorationInput|array<string, mixed> $graphqlCostDecoration
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/services/{ServiceIdOrName}/graphql-rate-limiting-advanced/costs', 'create-graphql-rate-limiting-advanced-cost-with-service', OperationScope::Both)]
    public function create(GraphqlCostDecorationInput|array $graphqlCostDecoration): GraphqlCostDecoration
    {
        return GraphqlCostDecoration::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, 'graphql-rate-limiting-advanced', 'costs'),
            body: $graphqlCostDecoration,
        ));
    }

    /**
     * Update fields of a GraphQL cost decoration (operationId `update-graphql-rate-limiting-advanced-cost-with-service`, PATCH, body `GraphQLCostDecoration`).
     *
     * @param GraphqlCostDecorationInput|array<string, mixed> $graphqlCostDecoration only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/services/{ServiceIdOrName}/graphql-rate-limiting-advanced/costs/{GraphQLCostDecorationId}', 'update-graphql-rate-limiting-advanced-cost-with-service', OperationScope::Both)]
    public function update(string $id, GraphqlCostDecorationInput|array $graphqlCostDecoration): GraphqlCostDecoration
    {
        return GraphqlCostDecoration::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, 'graphql-rate-limiting-advanced', 'costs', $id),
            body: $graphqlCostDecoration,
        ));
    }

    /**
     * Create or replace a GraphQL cost decoration by ID (operationId `upsert-graphql-rate-limiting-advanced-cost-with-service`, PUT, body `GraphQLCostDecorationWithoutParents`).
     *
     * @param GraphqlCostDecorationInput|array<string, mixed> $graphqlCostDecoration
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/services/{ServiceIdOrName}/graphql-rate-limiting-advanced/costs/{GraphQLCostDecorationId}', 'upsert-graphql-rate-limiting-advanced-cost-with-service', OperationScope::Both)]
    public function upsert(string $id, GraphqlCostDecorationInput|array $graphqlCostDecoration): GraphqlCostDecoration
    {
        return GraphqlCostDecoration::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, 'graphql-rate-limiting-advanced', 'costs', $id),
            body: $graphqlCostDecoration,
        ));
    }

    /**
     * Delete a GraphQL cost decoration (operationId `delete-graphql-rate-limiting-advanced-cost-with-service`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/services/{ServiceIdOrName}/graphql-rate-limiting-advanced/costs/{GraphQLCostDecorationId}', 'delete-graphql-rate-limiting-advanced-cost-with-service', OperationScope::Both)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, 'graphql-rate-limiting-advanced', 'costs', $id));
    }
}
