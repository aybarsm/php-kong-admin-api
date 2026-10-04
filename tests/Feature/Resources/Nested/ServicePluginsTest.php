<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\KongClient;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Models\PluginInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\TagFilter;
use Aybarsm\Kong\AdminApi\Resources\Nested\ServicePlugins;
use Aybarsm\Kong\AdminApi\Resources\Services;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(ServicePlugins::class, Services::class);

function servicePluginsUnderNestedTest(KongClient $client): ServicePlugins
{
    return $client->services()->plugins('parent 1');
}

it('sends every operation to the spec path', function (string $operation, string $method, string $path, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => [Fixture::get('plugin')]]),
        'delete' => MockKong::raw(204),
        default => MockKong::json(200, Fixture::get('plugin')),
    });
    $resource = servicePluginsUnderNestedTest($kong->client);

    match ($operation) {
        'list' => $resource->list(),
        'get' => $resource->get('item 1'),
        'create' => $resource->create(['name' => 'value']),
        'update' => $resource->update('item 1', ['name' => 'value']),
        'upsert' => $resource->upsert('item 1', new PluginInput()),
        'delete' => $resource->delete('item 1'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and($kong->lastRequest()->getBody()->__toString())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', '/services/parent%201/plugins', null],
    'get' => ['get', 'GET', '/services/parent%201/plugins/item%201', null],
    'create' => ['create', 'POST', '/services/parent%201/plugins', '{"name":"value"}'],
    'update' => ['update', 'PATCH', '/services/parent%201/plugins/item%201', '{"name":"value"}'],
    'upsert' => ['upsert', 'PUT', '/services/parent%201/plugins/item%201', '{}'],
    'delete' => ['delete', 'DELETE', '/services/parent%201/plugins/item%201', null],
]);

it('lists one page with options and walks every page', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('plugin')], 'offset' => 'p1']),
        MockKong::json(200, ['data' => [Fixture::get('plugin')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('plugin')]]),
    );
    $resource = servicePluginsUnderNestedTest($kong->client);

    $page = $resource->list(new ListOptions(size: 1, tags: TagFilter::allOf('a', 'b')));
    $rest = iterator_to_array($resource->all(new ListOptions(size: 1, offset: 'p1')));

    expect($page->data)->toEqual([Plugin::fromArray(Fixture::get('plugin'))])
        ->and($page->offset)->toBe('p1')
        ->and($kong->queryAt(0))->toBe(['size' => '1', 'tags' => 'a,b'])
        ->and($rest)->toHaveCount(2)
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p1'])
        ->and($kong->queryAt(2))->toBe(['size' => '1', 'offset' => 'p2'])
        ->and($kong->requestAt(2)->getUri()->getPath())->toBe('/services/parent%201/plugins');
});

it('fetches the next page with the same filters, and stops after the last', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('plugin')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('plugin')]]),
    );
    $resource = servicePluginsUnderNestedTest($kong->client);
    $options = new ListOptions(size: 1, tags: TagFilter::allOf('a'));

    $next = $resource->nextPage($resource->list($options), $options);

    expect($next?->data)->toHaveCount(1)
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p2', 'tags' => 'a'])
        ->and($next === null ? 'none' : $resource->nextPage($next, $options))->toBeNull()
        ->and($kong->requestCount())->toBe(2);
});

it('maps the response to Plugin', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('plugin')));

    expect(servicePluginsUnderNestedTest($kong->client)->get('x'))->toEqual(Plugin::fromArray(Fixture::get('plugin')));
});

it('prefixes the workspace', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('plugin')));

    servicePluginsUnderNestedTest($kong->client->inWorkspace('team-a'))->get('x');

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/team-a/services/parent%201/plugins/x');
});

it('rejects an empty ID before sending anything', function (): void {
    $kong = MockKong::queue();

    expect(fn (): Plugin => servicePluginsUnderNestedTest($kong->client)->get(''))->toThrow(InvalidArgumentException::class)
        ->and($kong->requestCount())->toBe(0);
});

it('maps 404 to NotFoundException', function (): void {
    $kong = MockKong::queue(MockKong::raw(404));

    expect(fn (): Plugin => servicePluginsUnderNestedTest($kong->client)->get('missing'))
        ->toThrow(NotFoundException::class, 'GET /services/parent%201/plugins/missing failed with HTTP 404.');
});

it('rejects an empty parent ID before sending anything', function (): void {
    expect(fn (): ServicePlugins => MockKong::queue()->client->services()->plugins(''))
        ->toThrow(InvalidArgumentException::class, 'Service ID or name must not be empty.');
});
