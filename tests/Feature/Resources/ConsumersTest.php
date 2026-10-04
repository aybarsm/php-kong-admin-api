<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\KongClient;
use Aybarsm\Kong\AdminApi\Models\Consumer;
use Aybarsm\Kong\AdminApi\Models\ConsumerInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\TagFilter;
use Aybarsm\Kong\AdminApi\Resources\Consumers;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(Consumers::class);

function consumersUnderTest(KongClient $client): Consumers
{
    return $client->consumers();
}

it('sends every operation to the spec path', function (string $operation, string $method, string $path, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => [Fixture::get('consumer')]]),
        'delete' => MockKong::raw(204),
        default => MockKong::json(200, Fixture::get('consumer')),
    });
    $resource = consumersUnderTest($kong->client);

    match ($operation) {
        'list' => $resource->list(),
        'get' => $resource->get('item 1'),
        'create' => $resource->create(['created_at' => 'value']),
        'update' => $resource->update('item 1', ['created_at' => 'value']),
        'upsert' => $resource->upsert('item 1', new ConsumerInput()),
        'delete' => $resource->delete('item 1'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and($kong->lastRequest()->getBody()->__toString())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', '/consumers', null],
    'get' => ['get', 'GET', '/consumers/item%201', null],
    'create' => ['create', 'POST', '/consumers', '{"created_at":"value"}'],
    'update' => ['update', 'PATCH', '/consumers/item%201', '{"created_at":"value"}'],
    'upsert' => ['upsert', 'PUT', '/consumers/item%201', '{}'],
    'delete' => ['delete', 'DELETE', '/consumers/item%201', null],
]);

it('lists one page with options and walks every page', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('consumer')], 'offset' => 'p1']),
        MockKong::json(200, ['data' => [Fixture::get('consumer')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('consumer')]]),
    );
    $resource = consumersUnderTest($kong->client);

    $page = $resource->list(new ListOptions(size: 1, tags: TagFilter::allOf('a', 'b')));
    $rest = iterator_to_array($resource->all(new ListOptions(size: 1, offset: 'p1')));

    expect($page->data)->toEqual([Consumer::fromArray(Fixture::get('consumer'))])
        ->and($page->offset)->toBe('p1')
        ->and($kong->queryAt(0))->toBe(['size' => '1', 'tags' => 'a,b'])
        ->and($rest)->toHaveCount(2)
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p1'])
        ->and($kong->queryAt(2))->toBe(['size' => '1', 'offset' => 'p2'])
        ->and($kong->requestAt(2)->getUri()->getPath())->toBe('/consumers');
});

it('maps the response to Consumer', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('consumer')));

    expect(consumersUnderTest($kong->client)->get('x'))->toEqual(Consumer::fromArray(Fixture::get('consumer')));
});

it('prefixes the workspace', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('consumer')));

    consumersUnderTest($kong->client->inWorkspace('team-a'))->get('x');

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/team-a/consumers/x');
});

it('rejects an empty ID before sending anything', function (): void {
    $kong = MockKong::queue();

    expect(fn (): Consumer => consumersUnderTest($kong->client)->get(''))->toThrow(InvalidArgumentException::class)
        ->and($kong->requestCount())->toBe(0);
});

it('maps 404 to NotFoundException', function (): void {
    $kong = MockKong::queue(MockKong::raw(404));

    expect(fn (): Consumer => consumersUnderTest($kong->client)->get('missing'))
        ->toThrow(NotFoundException::class, 'GET /consumers/missing failed with HTTP 404.');
});

it('filters by custom_id on list and all', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('consumer')]]),
        MockKong::json(200, ['data' => [Fixture::get('consumer')]]),
    );

    consumersUnderTest($kong->client)->list(customId: 'crm-42');
    iterator_to_array(consumersUnderTest($kong->client)->all(new ListOptions(size: 10), 'crm-42'));

    expect($kong->queryAt(0))->toBe(['custom_id' => 'crm-42'])
        ->and($kong->queryAt(1))->toBe(['size' => '10', 'custom_id' => 'crm-42']);
});
