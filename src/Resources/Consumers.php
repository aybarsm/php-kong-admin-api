<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\Consumer;
use Aybarsm\Kong\AdminApi\Models\ConsumerInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\Nested\ConsumerPlugins;
use Generator;

/**
 * Consumers (spec tag "Consumers"): `/consumers` and `/consumers/{ConsumerIdOrUsername}`.
 */
final readonly class Consumers extends AbstractResource
{
    private const string SEGMENT = 'consumers';

    /**
     * List one page of Consumers (operationId `list-consumer`).
     *
     * @param string|null $customId filter by `custom_id` (spec parameter `CustomId`)
     *
     * @return Page<Consumer>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/consumers', 'list-consumer', OperationScope::Both)]
    public function list(?ListOptions $options = null, ?string $customId = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), Consumer::fromArray(...), $options, ['custom_id' => $customId]);
    }

    /**
     * Lazily iterate every Consumer across all pages (operationId `list-consumer`).
     *
     * @param string|null $customId filter by `custom_id` (spec parameter `CustomId`)
     *
     * @return Generator<int, Consumer>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/consumers', 'list-consumer', OperationScope::Both)]
    public function all(?ListOptions $options = null, ?string $customId = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), Consumer::fromArray(...), $options, ['custom_id' => $customId]);
    }

    /**
     * Get a Consumer by ID or username (operationId `get-consumer`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrUsername is empty
     */
    #[Operation(Transport::METHOD_GET, '/consumers/{ConsumerIdOrUsername}', 'get-consumer', OperationScope::Both)]
    public function get(string $idOrUsername): Consumer
    {
        return Consumer::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrUsername),
        ));
    }

    /**
     * Create a Consumer (operationId `create-consumer`, body `Consumer`).
     *
     * @param ConsumerInput|array<string, mixed> $consumer
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/consumers', 'create-consumer', OperationScope::Both)]
    public function create(ConsumerInput|array $consumer): Consumer
    {
        return Consumer::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $consumer,
        ));
    }

    /**
     * Update fields of a Consumer (operationId `update-consumer`, PATCH, body `Consumer`).
     *
     * @param ConsumerInput|array<string, mixed> $consumer only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrUsername is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/consumers/{ConsumerIdOrUsername}', 'update-consumer', OperationScope::Both)]
    public function update(string $idOrUsername, ConsumerInput|array $consumer): Consumer
    {
        return Consumer::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrUsername),
            body: $consumer,
        ));
    }

    /**
     * Create or replace a Consumer by ID or username (operationId `upsert-consumer`, PUT, body `Consumer`).
     *
     * @param ConsumerInput|array<string, mixed> $consumer
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrUsername is empty
     */
    #[Operation(Transport::METHOD_PUT, '/consumers/{ConsumerIdOrUsername}', 'upsert-consumer', OperationScope::Both)]
    public function upsert(string $idOrUsername, ConsumerInput|array $consumer): Consumer
    {
        return Consumer::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrUsername),
            body: $consumer,
        ));
    }

    /**
     * Delete a Consumer (operationId `delete-consumer`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrUsername is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/consumers/{ConsumerIdOrUsername}', 'delete-consumer', OperationScope::Both)]
    public function delete(string $idOrUsername): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $idOrUsername));
    }

    /**
     * Plugins scoped to one Consumer: `/consumers/{ConsumerIdForNestedEntities}/plugins`.
     *
     * @throws InvalidArgumentException when $consumerId is empty
     */
    public function plugins(string $consumerId): ConsumerPlugins
    {
        if ($consumerId === '') {
            throw new InvalidArgumentException('Consumer ID must not be empty.');
        }

        return new ConsumerPlugins($this->transport, [...$this->parent, self::SEGMENT, $consumerId]);
    }
}
