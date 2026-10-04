<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\Partial;
use Aybarsm\Kong\AdminApi\Models\PartialEmbeddingsInput;
use Aybarsm\Kong\AdminApi\Models\PartialFactory;
use Aybarsm\Kong\AdminApi\Models\PartialModelInput;
use Aybarsm\Kong\AdminApi\Models\PartialRedisCeInput;
use Aybarsm\Kong\AdminApi\Models\PartialRedisEeInput;
use Aybarsm\Kong\AdminApi\Models\PartialVectordbInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\Nested\PartialLinks;
use Generator;

/**
 * Partials (spec tag "Partials"): `/partials` and `/partials/{PartialId}`.
 *
 * Responses are one of five variants chosen by `type` (see Partial and PartialFactory). Each variant has
 * its own input DTO, which sends the matching `type` by default.
 */
final readonly class Partials extends AbstractResource
{
    private const string SEGMENT = 'partials';

    /**
     * List one page of Partials (operationId `list-partial`).
     *
     * @return Page<Partial>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/partials', 'list-partial', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), PartialFactory::fromArray(...), $options);
    }

    /**
     * Lazily iterate every Partial across all pages (operationId `list-partial`).
     *
     * @return Generator<int, Partial>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/partials', 'list-partial', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), PartialFactory::fromArray(...), $options);
    }

    /**
     * The page after $page, or null when $page is the last (operationId `list-partial`).
     *
     * Repeats list() with the page's `offset`, keeping the same filters and workspace. Kong's own `next`
     * link is not followed (it drops filters on nested lists; spec-notes Q6).
     *
     * @param Page<Partial> $page
     *
     * @return Page<Partial>|null
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/partials', 'list-partial', OperationScope::Both)]
    public function nextPage(Page $page, ?ListOptions $options = null): ?Page
    {
        return $page->offset === null ? null : $this->list(($options ?? new ListOptions())->withOffset($page->offset));
    }

    /**
     * Get a Partial by ID (operationId `get-partial`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/partials/{PartialId}', 'get-partial', OperationScope::Both)]
    public function get(string $id): Partial
    {
        return PartialFactory::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
        ));
    }

    /**
     * Create a Partial (operationId `create-partial`).
     *
     * @param PartialRedisCeInput|PartialRedisEeInput|PartialVectordbInput|PartialEmbeddingsInput|PartialModelInput|array<string, mixed> $partial
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/partials', 'create-partial', OperationScope::Both)]
    public function create(PartialRedisCeInput|PartialRedisEeInput|PartialVectordbInput|PartialEmbeddingsInput|PartialModelInput|array $partial): Partial
    {
        return PartialFactory::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $partial,
        ));
    }

    /**
     * Update fields of a Partial (PATCH) (operationId `update-partial`).
     *
     * @param PartialRedisCeInput|PartialRedisEeInput|PartialVectordbInput|PartialEmbeddingsInput|PartialModelInput|array<string, mixed> $partial
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/partials/{PartialId}', 'update-partial', OperationScope::Both)]
    public function update(string $id, PartialRedisCeInput|PartialRedisEeInput|PartialVectordbInput|PartialEmbeddingsInput|PartialModelInput|array $partial): Partial
    {
        return PartialFactory::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $partial,
        ));
    }

    /**
     * Create or replace a Partial by ID (PUT) (operationId `upsert-partial`).
     *
     * @param PartialRedisCeInput|PartialRedisEeInput|PartialVectordbInput|PartialEmbeddingsInput|PartialModelInput|array<string, mixed> $partial
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/partials/{PartialId}', 'upsert-partial', OperationScope::Both)]
    public function upsert(string $id, PartialRedisCeInput|PartialRedisEeInput|PartialVectordbInput|PartialEmbeddingsInput|PartialModelInput|array $partial): Partial
    {
        return PartialFactory::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $partial,
        ));
    }

    /**
     * Delete a Partial. Kong answers 204 whether or not it existed (operationId `delete-partial`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/partials/{PartialId}', 'delete-partial', OperationScope::Both)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $id));
    }

    /**
     * Plugins linked to one Partial: `/partials/{PartialId}/links`.
     *
     * @throws InvalidArgumentException when $partialId is empty
     */
    public function links(string $partialId): PartialLinks
    {
        if ($partialId === '') {
            throw new InvalidArgumentException('Partial ID must not be empty.');
        }

        return new PartialLinks($this->transport, [...$this->parent, self::SEGMENT, $partialId]);
    }
}
