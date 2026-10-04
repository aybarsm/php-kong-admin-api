<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\EventHook;
use Aybarsm\Kong\AdminApi\Models\EventHookSourceEvents;
use Aybarsm\Kong\AdminApi\Models\EventHookSources;
use Aybarsm\Kong\AdminApi\Models\WebhookInput;
use Aybarsm\Kong\AdminApi\Pagination\Page;

/**
 * Event hooks (spec tag "Event-hooks"): `/event-hooks`, `/event-hooks/{eventHookId}/…` and
 * `/event-hooks/sources`. All paths are global. The spec defines no single-hook GET or PATCH.
 *
 * The spec returns its list envelope `EventHooksResponse` from list, create, ping and test, so all four
 * return a Page (spec-notes Q8). None declares pagination parameters, so there is no `all()` walker.
 */
final readonly class EventHooks extends AbstractResource
{
    private const string SEGMENT = 'event-hooks';

    /**
     * List event hooks (operationId `get-event-hooks`).
     *
     * @return Page<EventHook>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/event-hooks', 'get-event-hooks', OperationScope::GlobalOnly)]
    public function list(): Page
    {
        return $this->page($this->path(OperationScope::GlobalOnly, self::SEGMENT), EventHook::fromArray(...));
    }

    /**
     * Add an event hook (operationId `create-event-hooks`, body `AddWebhook`).
     *
     * @param WebhookInput|array<string, mixed> $hook
     *
     * @return Page<EventHook>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/event-hooks', 'create-event-hooks', OperationScope::GlobalOnly)]
    public function create(WebhookInput|array $hook): Page
    {
        return Page::fromArray(
            $this->object(Transport::METHOD_POST, $this->path(OperationScope::GlobalOnly, self::SEGMENT), body: $hook),
            EventHook::fromArray(...),
        );
    }

    /**
     * Delete an event hook (operationId `deleteEventHook`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/event-hooks/{eventHookId}', 'deleteEventHook', OperationScope::GlobalOnly)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id));
    }

    /**
     * Ping an event hook (operationId `get-event-hooks-event-hook-id-ping`).
     *
     * @return Page<EventHook>
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/event-hooks/{eventHookId}/ping', 'get-event-hooks-event-hook-id-ping', OperationScope::GlobalOnly)]
    public function ping(string $id): Page
    {
        return $this->page($this->path(OperationScope::GlobalOnly, self::SEGMENT, $id, 'ping'), EventHook::fromArray(...));
    }

    /**
     * Test an event hook (operationId `post-event-hooks-event-hook-id-test`, POST). The spec defines no body.
     *
     * @return Page<EventHook>
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_POST, '/event-hooks/{eventHookId}/test', 'post-event-hooks-event-hook-id-test', OperationScope::GlobalOnly)]
    public function test(string $id): Page
    {
        return Page::fromArray(
            $this->object(Transport::METHOD_POST, $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id, 'test')),
            EventHook::fromArray(...),
        );
    }

    /**
     * List the available event sources (operationId `get-event-hooks-sources`).
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/event-hooks/sources', 'get-event-hooks-sources', OperationScope::GlobalOnly)]
    public function sources(): EventHookSources
    {
        return EventHookSources::fromArray($this->object(Transport::METHOD_GET, $this->path(OperationScope::GlobalOnly, self::SEGMENT, 'sources')));
    }

    /**
     * List the events of one source (operationId `get-event-hooks-sources-source`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $source is empty
     */
    #[Operation(Transport::METHOD_GET, '/event-hooks/sources/{source}', 'get-event-hooks-sources-source', OperationScope::GlobalOnly)]
    public function sourceEvents(string $source): EventHookSourceEvents
    {
        return EventHookSourceEvents::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, 'sources', $source),
        ));
    }
}
