<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\Workspace;
use Aybarsm\Kong\AdminApi\Models\WorkspaceInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Generator;

/**
 * Workspaces (spec tag "Workspaces"): `/workspaces` and `/workspaces/{WorkspaceIdOrName}`.
 */
final readonly class Workspaces extends AbstractResource
{
    private const string SEGMENT = 'workspaces';

    /**
     * List one page of Workspaces (operationId `list-workspace`).
     *
     * @return Page<Workspace>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/workspaces', 'list-workspace', OperationScope::GlobalOnly)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::GlobalOnly, self::SEGMENT), Workspace::fromArray(...), $options);
    }

    /**
     * Lazily iterate every Workspace across all pages (operationId `list-workspace`).
     *
     * @return Generator<int, Workspace>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/workspaces', 'list-workspace', OperationScope::GlobalOnly)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::GlobalOnly, self::SEGMENT), Workspace::fromArray(...), $options);
    }

    /**
     * The page after $page, or null when $page is the last (operationId `list-workspace`).
     *
     * Repeats list() with the page's `offset`, keeping the same filters and workspace. Kong's own `next`
     * link is not followed (it drops filters on nested lists; spec-notes Q6).
     *
     * @param Page<Workspace> $page
     *
     * @return Page<Workspace>|null
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/workspaces', 'list-workspace', OperationScope::GlobalOnly)]
    public function nextPage(Page $page, ?ListOptions $options = null): ?Page
    {
        return $page->offset === null ? null : $this->list(($options ?? new ListOptions())->withOffset($page->offset));
    }

    /**
     * Get a Workspace by ID or name (operationId `get-workspace`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_GET, '/workspaces/{WorkspaceIdOrName}', 'get-workspace', OperationScope::GlobalOnly)]
    public function get(string $idOrName): Workspace
    {
        return Workspace::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $idOrName),
        ));
    }

    /**
     * Create a Workspace (operationId `create-workspace`, body `Workspace`).
     *
     * @param WorkspaceInput|array<string, mixed> $workspace
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/workspaces', 'create-workspace', OperationScope::GlobalOnly)]
    public function create(WorkspaceInput|array $workspace): Workspace
    {
        return Workspace::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT),
            body: $workspace,
        ));
    }

    /**
     * Update fields of a Workspace (operationId `update-workspace`, PATCH, body `Workspace`).
     *
     * @param WorkspaceInput|array<string, mixed> $workspace only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/workspaces/{WorkspaceIdOrName}', 'update-workspace', OperationScope::GlobalOnly)]
    public function update(string $idOrName, WorkspaceInput|array $workspace): Workspace
    {
        return Workspace::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $idOrName),
            body: $workspace,
        ));
    }

    /**
     * Create or replace a Workspace by ID or name (operationId `upsert-workspace`, PUT, body `Workspace`).
     *
     * @param WorkspaceInput|array<string, mixed> $workspace
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PUT, '/workspaces/{WorkspaceIdOrName}', 'upsert-workspace', OperationScope::GlobalOnly)]
    public function upsert(string $idOrName, WorkspaceInput|array $workspace): Workspace
    {
        return Workspace::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $idOrName),
            body: $workspace,
        ));
    }

    /**
     * Delete a Workspace (operationId `delete-workspace`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/workspaces/{WorkspaceIdOrName}', 'delete-workspace', OperationScope::GlobalOnly)]
    public function delete(string $idOrName): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::GlobalOnly, self::SEGMENT, $idOrName));
    }
}
