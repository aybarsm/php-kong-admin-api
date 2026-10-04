<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Enums\Protocol;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\Exceptions\UnauthorizedException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\Service;
use Aybarsm\Kong\AdminApi\Models\ServiceInput;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Aybarsm\Kong\AdminApi\Models\Shared\TlsSans;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\TagFilter;
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;
use Aybarsm\Kong\AdminApi\Resources\Services;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(Services::class, AbstractResource::class, Service::class, ServiceInput::class, ForeignKey::class, TlsSans::class, Transport::class);

it('lists one page with size, offset and an AND tag filter', function (): void {
    $kong = MockKong::queue(MockKong::json(200, [
        'data' => [Fixture::get('service')],
        'next' => '/services?offset=b2Zm',
        'offset' => 'b2Zm',
    ]));

    $page = $kong->client->services()->list(new ListOptions(size: 10, offset: 'c3Rh', tags: TagFilter::allOf('team-a', 'production')));

    expect($kong->lastRequest()->getMethod())->toBe('GET')
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/services')
        ->and($kong->lastQuery())->toBe(['size' => '10', 'offset' => 'c3Rh', 'tags' => 'team-a,production'])
        ->and($kong->lastRequest()->getHeaderLine('Accept'))->toBe('application/json')
        ->and($page->data)->toHaveCount(1)
        ->and($page->data[0]->host)->toBe('example.internal')
        ->and($page->offset)->toBe('b2Zm')
        ->and($page->next)->toBe('/services?offset=b2Zm')
        ->and($page->hasMore())->toBeTrue();
});

it('lists without a query string when no options are given', function (): void {
    $kong = MockKong::queue(MockKong::json(200, ['data' => [], 'next' => null]));

    $page = $kong->client->services()->list();

    expect($kong->lastRequest()->getUri()->getQuery())->toBe('')
        ->and($page->data)->toBe([])
        ->and($page->hasMore())->toBeFalse();
});

it('walks every page lazily, following offset', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('service')], 'offset' => 'p2', 'next' => '/services?offset=p2']),
        MockKong::json(200, ['data' => [Fixture::get('service'), Fixture::get('service')], 'offset' => 'p3']),
        MockKong::json(200, ['data' => [Fixture::get('service')], 'next' => null]),
    );

    $all = iterator_to_array($kong->client->services()->all(new ListOptions(size: 1, tags: TagFilter::anyOf('a', 'b'))));

    expect($all)->toHaveCount(4)
        ->and(array_keys($all))->toBe([0, 1, 2, 3])
        ->and($kong->requestCount())->toBe(3)
        ->and($kong->queryAt(0))->toBe(['size' => '1', 'tags' => 'a/b'])
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p2', 'tags' => 'a/b'])
        ->and($kong->queryAt(2))->toBe(['size' => '1', 'offset' => 'p3', 'tags' => 'a/b']);
});

it('does not send any request until the generator is consumed', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('service')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('service')]]),
    );

    $generator = $kong->client->services()->all();
    expect($kong->requestCount())->toBe(0);

    $generator->current();
    expect($kong->requestCount())->toBe(1);
});

it('stops walking when Kong repeats an offset', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [], 'offset' => 'same']),
        MockKong::json(200, ['data' => [], 'offset' => 'same']),
    );

    expect(fn (): array => iterator_to_array($kong->client->services()->all()))
        ->toThrow(UnexpectedResponseException::class, 'returned offset "same" twice');
});

it('gets a service by id or name and maps every field', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('service')));

    $service = $kong->client->services()->get('example-service');

    expect($kong->lastRequest()->getMethod())->toBe('GET')
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/services/example-service')
        ->and($service)->toEqual(new Service(
            host: 'example.internal',
            id: '49fd316e-c457-481c-9fc7-8079153e4f3c',
            name: 'example-service',
            protocol: Protocol::Http,
            port: 80,
            path: '/',
            retries: 5,
            connectTimeout: 60000,
            writeTimeout: 60000,
            readTimeout: 60000,
            enabled: true,
            caCertificates: ['4e3ad2e4-0bc4-4638-8e34-c84a417ba39b'],
            clientCertificate: new ForeignKey('51e77dc2-8f3e-4afa-9d0e-0e3bbbcfd515'),
            tlsSans: new TlsSans(['api.example.internal'], ['https://example.internal/id']),
            tlsVerify: true,
            tlsVerifyDepth: 3,
            tags: ['team-a', 'production'],
            createdAt: 1706598432,
            updatedAt: 1706684832,
        ));
});

it('round-trips the fixture through toArray', function (): void {
    expect(Service::fromArray(Fixture::get('service'))->toArray())->toEqual(Fixture::get('service'));
});

it('parses the example the spec gives for the Service schema', function (): void {
    $service = Service::fromArray(Fixture::specExample('Service'));

    expect($service->host)->toBe('example.internal')
        ->and($service->protocol)->toBe(Protocol::Http);
});

it('encodes ids and names as single path segments', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('service')));

    $kong->client->services()->get('a b/c');

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/services/a%20b%2Fc');
});

it('rejects an empty id before sending anything', function (): void {
    $kong = MockKong::queue();

    expect(fn (): Service => $kong->client->services()->get(''))->toThrow(InvalidArgumentException::class)
        ->and($kong->requestCount())->toBe(0);
});

it('prefixes the workspace on every operation', function (string $operation): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => []]),
        'delete' => MockKong::raw(204),
        default => MockKong::json(200, Fixture::get('service')),
    });
    $services = $kong->client->inWorkspace('team a')->services();

    match ($operation) {
        'list' => $services->list(),
        'delete' => $services->delete('svc'),
        'get' => $services->get('svc'),
        'create' => $services->create(['host' => 'h']),
        'update' => $services->update('svc', ['host' => 'h']),
        'upsert' => $services->upsert('svc', ['host' => 'h']),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getUri()->getPath())->toStartWith('/team%20a/services');
})->with(['list', 'get', 'create', 'update', 'upsert', 'delete']);

it('drops the workspace prefix again with withoutWorkspace()', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('service')));

    $kong->client->inWorkspace('team-a')->withoutWorkspace()->services()->get('svc');

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/services/svc');
});

it('creates a service from an input DTO without sending nulls', function (): void {
    $kong = MockKong::queue(MockKong::json(201, Fixture::get('service')));

    $service = $kong->client->services()->create(new ServiceInput(
        host: 'example.internal',
        name: 'example-service',
        protocol: Protocol::Https,
        clientCertificate: '51e77dc2-8f3e-4afa-9d0e-0e3bbbcfd515',
        tlsSans: new TlsSans(dnsnames: ['api.example.internal']),
        tags: ['team-a'],
    ));

    expect($kong->lastRequest()->getMethod())->toBe('POST')
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/services')
        ->and($kong->lastRequest()->getHeaderLine('Content-Type'))->toBe('application/json')
        ->and($kong->lastJsonBody())->toBe([
            'host' => 'example.internal',
            'name' => 'example-service',
            'protocol' => 'https',
            'client_certificate' => ['id' => '51e77dc2-8f3e-4afa-9d0e-0e3bbbcfd515'],
            'tls_sans' => ['dnsnames' => ['api.example.internal']],
            'tags' => ['team-a'],
        ])
        ->and($service->id)->toBe('49fd316e-c457-481c-9fc7-8079153e4f3c');
});

it('creates a service from an array, including the write-only url', function (): void {
    $kong = MockKong::queue(MockKong::json(201, Fixture::get('service')));

    $kong->client->services()->create(['url' => 'http://example.internal:80/', 'name' => 'example-service']);

    expect($kong->lastJsonBody())->toBe(['url' => 'http://example.internal:80/', 'name' => 'example-service']);
});

it('sends an empty JSON object for an empty array body', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('service')));

    $kong->client->services()->update('svc', []);

    expect((string) $kong->lastRequest()->getBody())->toBe('{}');
});

it('updates a service with PATCH, allowing explicit nulls via arrays', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('service')));

    $kong->client->services()->update('example-service', ['ca_certificates' => null, 'retries' => 3]);

    expect($kong->lastRequest()->getMethod())->toBe('PATCH')
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/services/example-service')
        ->and($kong->lastJsonBody())->toBe(['ca_certificates' => null, 'retries' => 3]);
});

it('upserts a service with PUT', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('service')));

    $kong->client->services()->upsert('example-service', new ServiceInput(host: 'example.internal', port: 8080));

    expect($kong->lastRequest()->getMethod())->toBe('PUT')
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/services/example-service')
        ->and($kong->lastJsonBody())->toBe(['host' => 'example.internal', 'port' => 8080]);
});

it('deletes a service and sends no body', function (): void {
    $kong = MockKong::queue(MockKong::raw(204));

    $kong->client->services()->delete('example-service');

    expect($kong->lastRequest()->getMethod())->toBe('DELETE')
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/services/example-service')
        ->and((string) $kong->lastRequest()->getBody())->toBe('');
});

it('maps 404 on get to NotFoundException', function (): void {
    $kong = MockKong::queue(MockKong::raw(404));

    expect(fn (): Service => $kong->client->services()->get('missing'))
        ->toThrow(NotFoundException::class, 'GET /services/missing failed with HTTP 404.');
});

it('maps 401 to UnauthorizedException with the spec error body', function (): void {
    $kong = MockKong::queue(MockKong::json(401, ['message' => 'Unauthorized', 'status' => 401]));

    try {
        $kong->client->services()->list();
        throw new RuntimeException('Expected an exception');
    } catch (UnauthorizedException $e) {
        expect($e->statusCode)->toBe(401)
            ->and($e->kongMessage)->toBe('Unauthorized')
            ->and($e->details)->toBe(['message' => 'Unauthorized', 'status' => 401])
            ->and($e->method)->toBe('GET')
            ->and($e->path)->toBe('/services');
    }
});

it('rejects a response missing the required host', function (): void {
    $kong = MockKong::queue(MockKong::json(200, ['id' => 'x']));

    expect(fn (): Service => $kong->client->services()->get('x'))
        ->toThrow(UnexpectedResponseException::class, 'Required field "host" is missing or null.');
});

it('rejects an unknown protocol value', function (): void {
    $kong = MockKong::queue(MockKong::json(200, [...Fixture::get('service'), 'protocol' => 'gopher']));

    expect(fn (): Service => $kong->client->services()->get('x'))
        ->toThrow(UnexpectedResponseException::class, 'Unexpected value for "protocol"');
});

it('rejects a JSON array where an object is expected', function (): void {
    $kong = MockKong::queue(MockKong::json(200, [Fixture::get('service')]));

    expect(fn (): Service => $kong->client->services()->get('x'))
        ->toThrow(UnexpectedResponseException::class, 'returned a JSON array where an object was expected');
});
