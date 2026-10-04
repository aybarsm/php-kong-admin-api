<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\KongClient;
use Aybarsm\Kong\AdminApi\Models\OidcJwk;
use Aybarsm\Kong\AdminApi\Models\OidcJwkInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\TagFilter;
use Aybarsm\Kong\AdminApi\Resources\OidcJwks;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(OidcJwks::class);

function oidcJwksUnderTest(KongClient $client): OidcJwks
{
    return $client->oidcJwks();
}

it('sends every operation to the spec path', function (string $operation, string $method, string $path, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => [Fixture::get('oidc_jwk')]]),
        'delete' => MockKong::raw(204),
        default => MockKong::json(200, Fixture::get('oidc_jwk')),
    });
    $resource = oidcJwksUnderTest($kong->client);

    match ($operation) {
        'list' => $resource->list(),
        'get' => $resource->get('item 1'),
        'create' => $resource->create(['jwks' => 'value']),
        'update' => $resource->update('item 1', ['jwks' => 'value']),
        'upsert' => $resource->upsert('item 1', new OidcJwkInput()),
        'delete' => $resource->delete('item 1'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and($kong->lastRequest()->getBody()->__toString())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', '/oic_jwks', null],
    'get' => ['get', 'GET', '/oic_jwks/item%201', null],
    'create' => ['create', 'POST', '/oic_jwks', '{"jwks":"value"}'],
    'update' => ['update', 'PATCH', '/oic_jwks/item%201', '{"jwks":"value"}'],
    'upsert' => ['upsert', 'PUT', '/oic_jwks/item%201', '{}'],
    'delete' => ['delete', 'DELETE', '/oic_jwks/item%201', null],
]);

it('lists one page with options and walks every page', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('oidc_jwk')], 'offset' => 'p1']),
        MockKong::json(200, ['data' => [Fixture::get('oidc_jwk')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('oidc_jwk')]]),
    );
    $resource = oidcJwksUnderTest($kong->client);

    $page = $resource->list(new ListOptions(size: 1, tags: TagFilter::allOf('a', 'b')));
    $rest = iterator_to_array($resource->all(new ListOptions(size: 1, offset: 'p1')));

    expect($page->data)->toEqual([OidcJwk::fromArray(Fixture::get('oidc_jwk'))])
        ->and($page->offset)->toBe('p1')
        ->and($kong->queryAt(0))->toBe(['size' => '1', 'tags' => 'a,b'])
        ->and($rest)->toHaveCount(2)
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p1'])
        ->and($kong->queryAt(2))->toBe(['size' => '1', 'offset' => 'p2'])
        ->and($kong->requestAt(2)->getUri()->getPath())->toBe('/oic_jwks');
});

it('maps the response to OidcJwk', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('oidc_jwk')));

    expect(oidcJwksUnderTest($kong->client)->get('x'))->toEqual(OidcJwk::fromArray(Fixture::get('oidc_jwk')));
});

it('prefixes the workspace', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('oidc_jwk')));

    oidcJwksUnderTest($kong->client->inWorkspace('team-a'))->get('x');

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/team-a/oic_jwks/x');
});

it('rejects an empty ID before sending anything', function (): void {
    $kong = MockKong::queue();

    expect(fn (): OidcJwk => oidcJwksUnderTest($kong->client)->get(''))->toThrow(InvalidArgumentException::class)
        ->and($kong->requestCount())->toBe(0);
});

it('maps 404 to NotFoundException', function (): void {
    $kong = MockKong::queue(MockKong::raw(404));

    expect(fn (): OidcJwk => oidcJwksUnderTest($kong->client)->get('missing'))
        ->toThrow(NotFoundException::class, 'GET /oic_jwks/missing failed with HTTP 404.');
});
