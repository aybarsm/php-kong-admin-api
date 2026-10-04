<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\Models\EventHook;
use Aybarsm\Kong\AdminApi\Models\EventHookSourceEvents;
use Aybarsm\Kong\AdminApi\Models\EventHookSources;
use Aybarsm\Kong\AdminApi\Models\WebhookHeaders;
use Aybarsm\Kong\AdminApi\Models\WebhookInput;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\EventHooks;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(EventHooks::class, WebhookInput::class, WebhookHeaders::class);

it('sends every operation to the spec path and body, never workspace-prefixed', function (string $operation, string $method, string $path, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'delete' => MockKong::raw(204),
        'sources' => MockKong::json(200, Fixture::get('event_hook_sources')),
        'sourceEvents' => MockKong::json(200, Fixture::get('event_hook_source_events')),
        default => MockKong::json(200, ['data' => [Fixture::get('event_hook')]]),
    });
    $hooks = $kong->client->inWorkspace('team-a')->eventHooks();

    match ($operation) {
        'list' => $hooks->list(),
        'create' => $hooks->create(new WebhookInput(
            configUrl: 'https://hooks.example.com',
            configHeaders: new WebhookHeaders(headers: 'x'),
            event: 'create',
            handler: 'webhook',
            source: 'crud',
        )),
        'delete' => $hooks->delete('h 1'),
        'ping' => $hooks->ping('h 1'),
        'test' => $hooks->test('h 1'),
        'sources' => $hooks->sources(),
        'sourceEvents' => $hooks->sourceEvents('crud'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and((string) $kong->lastRequest()->getBody())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', '/event-hooks', null],
    'create' => ['create', 'POST', '/event-hooks', '{"config.url":"https://hooks.example.com","handler":"webhook","source":"crud","config.headers":{"headers":"x"},"event":"create"}'],
    'delete' => ['delete', 'DELETE', '/event-hooks/h%201', null],
    'ping' => ['ping', 'GET', '/event-hooks/h%201/ping', null],
    'test' => ['test', 'POST', '/event-hooks/h%201/test', null],
    'sources' => ['sources', 'GET', '/event-hooks/sources', null],
    'sourceEvents' => ['sourceEvents', 'GET', '/event-hooks/sources/crud', null],
]);

it('returns the spec list envelope from list, create, ping and test (spec-notes Q8)', function (string $operation): void {
    $kong = MockKong::queue(MockKong::json(200, ['data' => [Fixture::get('event_hook')], 'next' => null]));
    $hooks = $kong->client->eventHooks();

    $page = match ($operation) {
        'list' => $hooks->list(),
        'create' => $hooks->create([]),
        'ping' => $hooks->ping('h'),
        'test' => $hooks->test('h'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($page)->toEqual(new Page([EventHook::fromArray(Fixture::get('event_hook'))]));
})->with(['list', 'create', 'ping', 'test']);

it('maps the sources responses with free-form data (spec-notes Q17)', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => ['crud' => ['services' => ['create' => []]]]]),
        MockKong::json(200, ['data' => ['create' => ['fields' => ['entity']]]]),
    );

    expect($kong->client->eventHooks()->sources())->toEqual(new EventHookSources(['crud' => ['services' => ['create' => []]]]))
        ->and($kong->client->eventHooks()->sourceEvents('crud'))->toEqual(new EventHookSourceEvents(['create' => ['fields' => ['entity']]]));
});

it('maps 404 on delete and rejects empty IDs', function (): void {
    $kong = MockKong::queue(MockKong::raw(404));

    expect(fn () => $kong->client->eventHooks()->delete('missing'))->toThrow(NotFoundException::class)
        ->and(fn (): Page => $kong->client->eventHooks()->ping(''))->toThrow(InvalidArgumentException::class)
        ->and(fn (): EventHookSourceEvents => $kong->client->eventHooks()->sourceEvents(''))->toThrow(InvalidArgumentException::class);
});

it('round-trips the webhook headers object', function (): void {
    expect(WebhookHeaders::fromArray(['headers' => 'x-a'])->toArray())->toBe(['headers' => 'x-a'])
        ->and(WebhookHeaders::fromArray([])->toArray())->toBe([]);
});
