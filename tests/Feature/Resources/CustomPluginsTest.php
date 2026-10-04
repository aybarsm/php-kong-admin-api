<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\KongClient;
use Aybarsm\Kong\AdminApi\Models\CustomPlugin;
use Aybarsm\Kong\AdminApi\Models\CustomPluginInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\TagFilter;
use Aybarsm\Kong\AdminApi\Resources\CustomPlugins;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(CustomPlugins::class);

function customPluginsUnderTest(KongClient $client): CustomPlugins
{
    return $client->customPlugins();
}

it('sends every operation to the spec path', function (string $operation, string $method, string $path, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => [Fixture::get('custom_plugin')]]),
        'delete' => MockKong::raw(204),
        default => MockKong::json(200, Fixture::get('custom_plugin')),
    });
    $resource = customPluginsUnderTest($kong->client);

    match ($operation) {
        'list' => $resource->list(),
        'get' => $resource->get('item 1'),
        'create' => $resource->create(['handler' => 'value']),
        'update' => $resource->update('item 1', ['handler' => 'value']),
        'upsert' => $resource->upsert('item 1', new CustomPluginInput()),
        'delete' => $resource->delete('item 1'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and($kong->lastRequest()->getBody()->__toString())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', '/custom-plugins', null],
    'get' => ['get', 'GET', '/custom-plugins/item%201', null],
    'create' => ['create', 'POST', '/custom-plugins', '{"handler":"value"}'],
    'update' => ['update', 'PATCH', '/custom-plugins/item%201', '{"handler":"value"}'],
    'upsert' => ['upsert', 'PUT', '/custom-plugins/item%201', '{}'],
    'delete' => ['delete', 'DELETE', '/custom-plugins/item%201', null],
]);

it('lists one page with options and walks every page', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('custom_plugin')], 'offset' => 'p1']),
        MockKong::json(200, ['data' => [Fixture::get('custom_plugin')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('custom_plugin')]]),
    );
    $resource = customPluginsUnderTest($kong->client);

    $page = $resource->list(new ListOptions(size: 1, tags: TagFilter::allOf('a', 'b')));
    $rest = iterator_to_array($resource->all(new ListOptions(size: 1, offset: 'p1')));

    expect($page->data)->toEqual([CustomPlugin::fromArray(Fixture::get('custom_plugin'))])
        ->and($page->offset)->toBe('p1')
        ->and($kong->queryAt(0))->toBe(['size' => '1', 'tags' => 'a,b'])
        ->and($rest)->toHaveCount(2)
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p1'])
        ->and($kong->queryAt(2))->toBe(['size' => '1', 'offset' => 'p2'])
        ->and($kong->requestAt(2)->getUri()->getPath())->toBe('/custom-plugins');
});

it('maps the response to CustomPlugin', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('custom_plugin')));

    expect(customPluginsUnderTest($kong->client)->get('x'))->toEqual(CustomPlugin::fromArray(Fixture::get('custom_plugin')));
});

it('never prefixes a workspace on these global-only paths', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('custom_plugin')));

    customPluginsUnderTest($kong->client->inWorkspace('team-a'))->get('x');

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/custom-plugins/x');
});

it('rejects an empty ID before sending anything', function (): void {
    $kong = MockKong::queue();

    expect(fn (): CustomPlugin => customPluginsUnderTest($kong->client)->get(''))->toThrow(InvalidArgumentException::class)
        ->and($kong->requestCount())->toBe(0);
});

it('maps 404 to NotFoundException', function (): void {
    $kong = MockKong::queue(MockKong::raw(404));

    expect(fn (): CustomPlugin => customPluginsUnderTest($kong->client)->get('missing'))
        ->toThrow(NotFoundException::class, 'GET /custom-plugins/missing failed with HTTP 404.');
});
