<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\KongClient;
use Aybarsm\Kong\AdminApi\Models\Sni;
use Aybarsm\Kong\AdminApi\Models\SniInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\TagFilter;
use Aybarsm\Kong\AdminApi\Resources\Certificates;
use Aybarsm\Kong\AdminApi\Resources\Nested\CertificateSnis;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(CertificateSnis::class, Certificates::class);

function certificateSnisUnderNestedTest(KongClient $client): CertificateSnis
{
    return $client->certificates()->snis('parent 1');
}

it('sends every operation to the spec path', function (string $operation, string $method, string $path, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => [Fixture::get('sni')]]),
        'delete' => MockKong::raw(204),
        default => MockKong::json(200, Fixture::get('sni')),
    });
    $resource = certificateSnisUnderNestedTest($kong->client);

    match ($operation) {
        'list' => $resource->list(),
        'get' => $resource->get('item 1'),
        'create' => $resource->create(['name' => 'value']),
        'update' => $resource->update('item 1', ['name' => 'value']),
        'upsert' => $resource->upsert('item 1', new SniInput()),
        'delete' => $resource->delete('item 1'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and($kong->lastRequest()->getBody()->__toString())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', '/certificates/parent%201/snis', null],
    'get' => ['get', 'GET', '/certificates/parent%201/snis/item%201', null],
    'create' => ['create', 'POST', '/certificates/parent%201/snis', '{"name":"value"}'],
    'update' => ['update', 'PATCH', '/certificates/parent%201/snis/item%201', '{"name":"value"}'],
    'upsert' => ['upsert', 'PUT', '/certificates/parent%201/snis/item%201', '{}'],
    'delete' => ['delete', 'DELETE', '/certificates/parent%201/snis/item%201', null],
]);

it('lists one page with options and walks every page', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('sni')], 'offset' => 'p1']),
        MockKong::json(200, ['data' => [Fixture::get('sni')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('sni')]]),
    );
    $resource = certificateSnisUnderNestedTest($kong->client);

    $page = $resource->list(new ListOptions(size: 1, tags: TagFilter::allOf('a', 'b')));
    $rest = iterator_to_array($resource->all(new ListOptions(size: 1, offset: 'p1')));

    expect($page->data)->toEqual([Sni::fromArray(Fixture::get('sni'))])
        ->and($page->offset)->toBe('p1')
        ->and($kong->queryAt(0))->toBe(['size' => '1', 'tags' => 'a,b'])
        ->and($rest)->toHaveCount(2)
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p1'])
        ->and($kong->queryAt(2))->toBe(['size' => '1', 'offset' => 'p2'])
        ->and($kong->requestAt(2)->getUri()->getPath())->toBe('/certificates/parent%201/snis');
});

it('maps the response to Sni', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('sni')));

    expect(certificateSnisUnderNestedTest($kong->client)->get('x'))->toEqual(Sni::fromArray(Fixture::get('sni')));
});

it('prefixes the workspace', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('sni')));

    certificateSnisUnderNestedTest($kong->client->inWorkspace('team-a'))->get('x');

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/team-a/certificates/parent%201/snis/x');
});

it('rejects an empty ID before sending anything', function (): void {
    $kong = MockKong::queue();

    expect(fn (): Sni => certificateSnisUnderNestedTest($kong->client)->get(''))->toThrow(InvalidArgumentException::class)
        ->and($kong->requestCount())->toBe(0);
});

it('maps 404 to NotFoundException', function (): void {
    $kong = MockKong::queue(MockKong::raw(404));

    expect(fn (): Sni => certificateSnisUnderNestedTest($kong->client)->get('missing'))
        ->toThrow(NotFoundException::class, 'GET /certificates/parent%201/snis/missing failed with HTTP 404.');
});

it('rejects an empty parent ID before sending anything', function (): void {
    expect(fn (): CertificateSnis => MockKong::queue()->client->certificates()->snis(''))
        ->toThrow(InvalidArgumentException::class, 'Certificate ID must not be empty.');
});
