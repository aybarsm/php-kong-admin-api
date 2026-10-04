<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Enums\HttpsRedirectStatusCode;
use Aybarsm\Kong\AdminApi\Enums\PathHandling;
use Aybarsm\Kong\AdminApi\Enums\Protocol;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Models\Route;
use Aybarsm\Kong\AdminApi\Models\RouteExpression;
use Aybarsm\Kong\AdminApi\Models\RouteExpressionInput;
use Aybarsm\Kong\AdminApi\Models\RouteFactory;
use Aybarsm\Kong\AdminApi\Models\RouteJson;
use Aybarsm\Kong\AdminApi\Models\RouteJsonInput;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Aybarsm\Kong\AdminApi\Models\Shared\IpPort;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;

covers(RouteFactory::class, RouteJson::class, RouteExpression::class, RouteJsonInput::class, RouteExpressionInput::class, IpPort::class);

it('maps a payload with a non-null expression to RouteExpression (spec-notes Q1)', function (): void {
    expect(RouteFactory::fromArray(Fixture::get('route_expression')))->toBeInstanceOf(RouteExpression::class);
});

it('maps every other payload to RouteJson', function (array $payload): void {
    expect(RouteFactory::fromArray($payload))->toBeInstanceOf(RouteJson::class);
})->with([
    'classic route' => [Fixture::get('route_json')],
    'shared fields only' => [['id' => 'r1', 'name' => 'r1', 'protocols' => ['http']]],
    'null expression' => [['id' => 'r1', 'expression' => null, 'paths' => ['/']]],
    'empty object' => [[]],
]);

it('rejects a non-string expression', function (): void {
    expect(fn (): Route => RouteFactory::fromArray(['expression' => 42]))
        ->toThrow(UnexpectedResponseException::class, 'Field "expression" is not a valid string.');
});

it('maps every RouteJson field', function (): void {
    expect(RouteJson::fromArray(Fixture::get('route_json')))->toEqual(new RouteJson(
        id: '56c4566c-14cc-4132-9011-4139fcbbe50a',
        name: 'example-route',
        protocols: [Protocol::Http, Protocol::Https],
        methods: ['GET', 'POST'],
        hosts: ['foo.example.com', 'foo.example.us'],
        paths: ['/v1', '/v2'],
        headers: ['x-team' => ['payments', 'billing'], 'x-version' => ['~*v[12]']],
        snis: ['foo.example.com'],
        sources: [new IpPort(ip: '192.168.0.0/16'), new IpPort(port: 8000)],
        destinations: [new IpPort('10.0.0.0/8', 443)],
        httpsRedirectStatusCode: HttpsRedirectStatusCode::UpgradeRequired,
        regexPriority: 0,
        stripPath: true,
        pathHandling: PathHandling::V0,
        preserveHost: false,
        requestBuffering: true,
        responseBuffering: true,
        service: new ForeignKey('bd380f99-659d-415e-b0e7-72ea05df3218'),
        tags: ['team-a'],
        createdAt: 1706598432,
        updatedAt: 1706684832,
    ));
});

it('maps every RouteExpression field', function (): void {
    expect(RouteExpression::fromArray(Fixture::get('route_expression')))->toEqual(new RouteExpression(
        expression: 'http.path ^= "/v1" && http.method == "GET"',
        id: '0a9d1e3c-6b4f-4b6a-9f15-5c2a3f9a1d77',
        name: 'expression-route',
        priority: 100,
        protocols: [Protocol::Grpc, Protocol::Grpcs],
        httpsRedirectStatusCode: HttpsRedirectStatusCode::PermanentRedirect,
        stripPath: false,
        pathHandling: PathHandling::V1,
        preserveHost: true,
        requestBuffering: false,
        responseBuffering: false,
        service: new ForeignKey('bd380f99-659d-415e-b0e7-72ea05df3218'),
        tags: ['expr'],
        createdAt: 1706598432,
        updatedAt: 1706684832,
    ));
});

it('round-trips both variants through toArray', function (string $fixture): void {
    expect(RouteFactory::fromArray(Fixture::get($fixture))->toArray())->toEqual(Fixture::get($fixture));
})->with(['route_json', 'route_expression']);

it('parses the example the spec gives for RouteJson', function (): void {
    $route = RouteFactory::fromArray(Fixture::specExample('RouteJson'));

    expect($route)->toBeInstanceOf(RouteJson::class);
    /** @var RouteJson $route */
    expect($route->paths)->toBe(['/v1', '/v2'])
        ->and($route->service?->id)->toBe('bd380f99-659d-415e-b0e7-72ea05df3218');
});

it('rejects malformed headers, addresses and enum values', function (array $payload, string $message): void {
    expect(fn (): Route => RouteJson::fromArray($payload))->toThrow(UnexpectedResponseException::class, $message);
})->with([
    'headers as list' => [['headers' => [['a']]], 'Field "headers" is not a valid object.'],
    'header values not a list' => [['headers' => ['x-a' => 'b']], 'Field "headers.x-a" is not a valid list of strings.'],
    'header value not a string' => [['headers' => ['x-a' => [1]]], 'Field "headers.x-a" is not a valid list of strings.'],
    'source not an object' => [['sources' => ['1.2.3.4']], 'Field "sources[0]" is not a valid object.'],
    'unknown protocol' => [['protocols' => ['gopher']], 'Unexpected value for "protocols[0]"'],
    'unknown redirect code' => [['https_redirect_status_code' => 303], 'Unexpected value for "https_redirect_status_code"'],
]);

it('serialises a RouteJson input without nulls, wrapping service IDs', function (): void {
    $input = new RouteJsonInput(
        name: 'r',
        protocols: [Protocol::Https],
        paths: ['/api'],
        headers: ['x-team' => ['a']],
        sources: [new IpPort('10.0.0.1', 80)],
        httpsRedirectStatusCode: HttpsRedirectStatusCode::PermanentRedirect,
        pathHandling: PathHandling::V1,
        service: 'svc-id',
    );

    expect($input->toArray())->toBe([
        'name' => 'r',
        'protocols' => ['https'],
        'paths' => ['/api'],
        'headers' => ['x-team' => ['a']],
        'sources' => [['ip' => '10.0.0.1', 'port' => 80]],
        'https_redirect_status_code' => 308,
        'path_handling' => 'v1',
        'service' => ['id' => 'svc-id'],
    ])->and((new RouteJsonInput())->toArray())->toBe([]);
});

it('serialises a RouteExpression input without nulls', function (): void {
    $input = new RouteExpressionInput(
        expression: 'http.path == "/"',
        priority: 5,
        protocols: [Protocol::Http],
        service: new ForeignKey('svc-id'),
    );

    expect($input->toArray())->toBe([
        'expression' => 'http.path == "/"',
        'priority' => 5,
        'protocols' => ['http'],
        'service' => ['id' => 'svc-id'],
    ])->and((new RouteExpressionInput())->toArray())->toBe([]);
});
