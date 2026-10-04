<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources\Nested;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\Target;
use Aybarsm\Kong\AdminApi\Models\TargetInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;
use Generator;

/**
 * Targets nested under one Upstream: `/upstreams/{UpstreamIdForTarget}/targets` and `/upstreams/{UpstreamIdForTarget}/targets/{TargetIdOrTarget}`.
 *
 * Obtain it with `$client->upstreams()->targets($upstreamId)`.
 */
final readonly class UpstreamTargets extends AbstractResource
{
    private const string SEGMENT = 'targets';

    /**
     * List one page of Targets (operationId `list-target-with-upstream`).
     *
     * @return Page<Target>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/upstreams/{UpstreamIdForTarget}/targets', 'list-target-with-upstream', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), Target::fromArray(...), $options);
    }

    /**
     * Lazily iterate every Target across all pages (operationId `list-target-with-upstream`).
     *
     * @return Generator<int, Target>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/upstreams/{UpstreamIdForTarget}/targets', 'list-target-with-upstream', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), Target::fromArray(...), $options);
    }

    /**
     * Get a Target by ID or target (`host:port`) (operationId `get-target-with-upstream`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrTarget is empty
     */
    #[Operation(Transport::METHOD_GET, '/upstreams/{UpstreamIdForTarget}/targets/{TargetIdOrTarget}', 'get-target-with-upstream', OperationScope::Both)]
    public function get(string $idOrTarget): Target
    {
        return Target::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrTarget),
        ));
    }

    /**
     * Create a Target (operationId `create-target-with-upstream`, body `TargetWithoutParents`).
     *
     * @param TargetInput|array<string, mixed> $target
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/upstreams/{UpstreamIdForTarget}/targets', 'create-target-with-upstream', OperationScope::Both)]
    public function create(TargetInput|array $target): Target
    {
        return Target::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $target,
        ));
    }

    /**
     * Update fields of a Target (operationId `update-target-with-upstream`, PATCH, body `Target`).
     *
     * @param TargetInput|array<string, mixed> $target only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrTarget is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/upstreams/{UpstreamIdForTarget}/targets/{TargetIdOrTarget}', 'update-target-with-upstream', OperationScope::Both)]
    public function update(string $idOrTarget, TargetInput|array $target): Target
    {
        return Target::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrTarget),
            body: $target,
        ));
    }

    /**
     * Create or replace a Target by ID or target (`host:port`) (operationId `upsert-target-with-upstream`, PUT, body `TargetWithoutParents`).
     *
     * @param TargetInput|array<string, mixed> $target
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrTarget is empty
     */
    #[Operation(Transport::METHOD_PUT, '/upstreams/{UpstreamIdForTarget}/targets/{TargetIdOrTarget}', 'upsert-target-with-upstream', OperationScope::Both)]
    public function upsert(string $idOrTarget, TargetInput|array $target): Target
    {
        return Target::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrTarget),
            body: $target,
        ));
    }

    /**
     * Delete a Target (operationId `delete-target-with-upstream`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrTarget is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/upstreams/{UpstreamIdForTarget}/targets/{TargetIdOrTarget}', 'delete-target-with-upstream', OperationScope::Both)]
    public function delete(string $idOrTarget): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $idOrTarget));
    }
}
