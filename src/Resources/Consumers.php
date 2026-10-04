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
use Aybarsm\Kong\AdminApi\Resources\Nested\ConsumerAcls;
use Aybarsm\Kong\AdminApi\Resources\Nested\ConsumerBasicAuths;
use Aybarsm\Kong\AdminApi\Resources\Nested\ConsumerConsumerGroups;
use Aybarsm\Kong\AdminApi\Resources\Nested\ConsumerHmacAuths;
use Aybarsm\Kong\AdminApi\Resources\Nested\ConsumerJwts;
use Aybarsm\Kong\AdminApi\Resources\Nested\ConsumerKeyAuths;
use Aybarsm\Kong\AdminApi\Resources\Nested\ConsumerMtlsAuths;
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
     * The page after $page, or null when $page is the last (operationId `list-consumer`).
     *
     * Repeats list() with the page's `offset`, keeping the same filters and workspace. Kong's own `next`
     * link is not followed (it drops filters on nested lists; spec-notes Q6).
     *
     * @param Page<Consumer> $page
     * @param string|null $customId filter by `custom_id` (spec parameter `CustomId`)
     *
     * @return Page<Consumer>|null
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/consumers', 'list-consumer', OperationScope::Both)]
    public function nextPage(Page $page, ?ListOptions $options = null, ?string $customId = null): ?Page
    {
        return $page->offset === null ? null : $this->list(($options ?? new ListOptions())->withOffset($page->offset), $customId);
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

    /**
     * ACLs of one Consumer: `/consumers/{ConsumerIdForNestedEntities}/acls`.
     *
     * @throws InvalidArgumentException when $consumerId is empty
     */
    public function acls(string $consumerId): ConsumerAcls
    {
        if ($consumerId === '') {
            throw new InvalidArgumentException('Consumer ID must not be empty.');
        }

        return new ConsumerAcls($this->transport, [...$this->parent, self::SEGMENT, $consumerId]);
    }

    /**
     * API keys of one Consumer: `/consumers/{ConsumerIdForNestedEntities}/key-auth`.
     *
     * @throws InvalidArgumentException when $consumerId is empty
     */
    public function keyAuths(string $consumerId): ConsumerKeyAuths
    {
        if ($consumerId === '') {
            throw new InvalidArgumentException('Consumer ID must not be empty.');
        }

        return new ConsumerKeyAuths($this->transport, [...$this->parent, self::SEGMENT, $consumerId]);
    }

    /**
     * Basic-auth credentials of one Consumer: `/consumers/{ConsumerIdForNestedEntities}/basic-auth`.
     *
     * @throws InvalidArgumentException when $consumerId is empty
     */
    public function basicAuths(string $consumerId): ConsumerBasicAuths
    {
        if ($consumerId === '') {
            throw new InvalidArgumentException('Consumer ID must not be empty.');
        }

        return new ConsumerBasicAuths($this->transport, [...$this->parent, self::SEGMENT, $consumerId]);
    }

    /**
     * HMAC-auth credentials of one Consumer: `/consumers/{ConsumerIdForNestedEntities}/hmac-auth`.
     *
     * @throws InvalidArgumentException when $consumerId is empty
     */
    public function hmacAuths(string $consumerId): ConsumerHmacAuths
    {
        if ($consumerId === '') {
            throw new InvalidArgumentException('Consumer ID must not be empty.');
        }

        return new ConsumerHmacAuths($this->transport, [...$this->parent, self::SEGMENT, $consumerId]);
    }

    /**
     * JWTs of one Consumer: `/consumers/{ConsumerIdForNestedEntities}/jwt`.
     *
     * @throws InvalidArgumentException when $consumerId is empty
     */
    public function jwts(string $consumerId): ConsumerJwts
    {
        if ($consumerId === '') {
            throw new InvalidArgumentException('Consumer ID must not be empty.');
        }

        return new ConsumerJwts($this->transport, [...$this->parent, self::SEGMENT, $consumerId]);
    }

    /**
     * MTLS-auth credentials of one Consumer: `/consumers/{ConsumerIdForNestedEntities}/mtls-auth`.
     *
     * @throws InvalidArgumentException when $consumerId is empty
     */
    public function mtlsAuths(string $consumerId): ConsumerMtlsAuths
    {
        if ($consumerId === '') {
            throw new InvalidArgumentException('Consumer ID must not be empty.');
        }

        return new ConsumerMtlsAuths($this->transport, [...$this->parent, self::SEGMENT, $consumerId]);
    }

    /**
     * Consumer Group memberships of one Consumer: `/consumers/{ConsumerIdOrUsername}/consumer_groups`.
     *
     * @throws InvalidArgumentException when $idOrUsername is empty
     */
    public function consumerGroups(string $idOrUsername): ConsumerConsumerGroups
    {
        if ($idOrUsername === '') {
            throw new InvalidArgumentException('Consumer ID or username must not be empty.');
        }

        return new ConsumerConsumerGroups($this->transport, [...$this->parent, self::SEGMENT, $idOrUsername]);
    }
}
