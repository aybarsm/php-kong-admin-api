<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\Upstream;
use Aybarsm\Kong\AdminApi\Models\UpstreamInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\Nested\UpstreamTargets;
use Generator;

/**
 * Upstreams (spec tag "Upstreams"): `/upstreams` and `/upstreams/{UpstreamIdOrName}`.
 */
final readonly class Upstreams extends AbstractResource
{
    private const string SEGMENT = 'upstreams';

    /**
     * List one page of Upstreams (operationId `list-upstream`).
     *
     * @return Page<Upstream>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/upstreams', 'list-upstream', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), Upstream::fromArray(...), $options);
    }

    /**
     * Lazily iterate every Upstream across all pages (operationId `list-upstream`).
     *
     * @return Generator<int, Upstream>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/upstreams', 'list-upstream', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), Upstream::fromArray(...), $options);
    }

    /**
     * Get an Upstream by ID or name (operationId `get-upstream`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_GET, '/upstreams/{UpstreamIdOrName}', 'get-upstream', OperationScope::Both)]
    public function get(string $idOrName): Upstream
    {
        return Upstream::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
        ));
    }

    /**
     * Create an Upstream (operationId `create-upstream`, body `Upstream`).
     *
     * @param UpstreamInput|array<string, mixed> $upstream
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/upstreams', 'create-upstream', OperationScope::Both)]
    public function create(UpstreamInput|array $upstream): Upstream
    {
        return Upstream::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $upstream,
        ));
    }

    /**
     * Update fields of an Upstream (operationId `update-upstream`, PATCH, body `Upstream`).
     *
     * @param UpstreamInput|array<string, mixed> $upstream only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/upstreams/{UpstreamIdOrName}', 'update-upstream', OperationScope::Both)]
    public function update(string $idOrName, UpstreamInput|array $upstream): Upstream
    {
        return Upstream::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
            body: $upstream,
        ));
    }

    /**
     * Create or replace an Upstream by ID or name (operationId `upsert-upstream`, PUT, body `Upstream`).
     *
     * @param UpstreamInput|array<string, mixed> $upstream
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PUT, '/upstreams/{UpstreamIdOrName}', 'upsert-upstream', OperationScope::Both)]
    public function upsert(string $idOrName, UpstreamInput|array $upstream): Upstream
    {
        return Upstream::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
            body: $upstream,
        ));
    }

    /**
     * Delete an Upstream (operationId `delete-upstream`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/upstreams/{UpstreamIdOrName}', 'delete-upstream', OperationScope::Both)]
    public function delete(string $idOrName): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $idOrName));
    }

    /**
     * Targets of one Upstream: `/upstreams/{UpstreamIdForTarget}/targets`.
     *
     * @throws InvalidArgumentException when $upstreamId is empty
     */
    public function targets(string $upstreamId): UpstreamTargets
    {
        if ($upstreamId === '') {
            throw new InvalidArgumentException('Upstream ID must not be empty.');
        }

        return new UpstreamTargets($this->transport, [...$this->parent, self::SEGMENT, $upstreamId]);
    }
}
