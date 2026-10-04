<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Config\ClientConfig;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\ConflictException;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\Exceptions\ServerException;
use Aybarsm\Kong\AdminApi\Exceptions\TransportException;
use Aybarsm\Kong\AdminApi\Exceptions\UnauthorizedException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Exceptions\ValidationException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\Service;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

covers(Transport::class, KongApiException::class);

/**
 * @param class-string<KongApiException> $expected
 */
function expectKongError(MockKong $kong, string $expected, ?int $status, ?string $kongMessage): KongApiException
{
    try {
        $kong->client->services()->get('svc');
    } catch (KongApiException $e) {
        expect($e)->toBeInstanceOf($expected)
            ->and($e->statusCode)->toBe($status)
            ->and($e->kongMessage)->toBe($kongMessage);

        return $e;
    }

    throw new RuntimeException('Expected ' . $expected);
}

it('maps HTTP error statuses to exception classes', function (int $status, string $expected): void {
    /** @var class-string<KongApiException> $expected */
    $kong = MockKong::queue(MockKong::json($status, ['message' => 'boom', 'status' => $status]));

    $e = expectKongError($kong, $expected, $status, 'boom');

    expect($e->getMessage())->toBe(sprintf('GET /services/svc failed with HTTP %d: boom', $status))
        ->and($e->getCode())->toBe($status)
        ->and($e->details)->toBe(['message' => 'boom', 'status' => $status]);
})->with([
    'validation 400' => [400, ValidationException::class],
    'unauthorized 401' => [401, UnauthorizedException::class],
    'forbidden 403 (not in spec)' => [403, KongApiException::class],
    'not found 404' => [404, NotFoundException::class],
    'method not allowed 405' => [405, KongApiException::class],
    'conflict 409' => [409, ConflictException::class],
    'server 500' => [500, ServerException::class],
    'not implemented 501' => [501, ServerException::class],
    'unavailable 503' => [503, ServerException::class],
]);

it('tolerates error bodies that are empty, not JSON or not objects', function (string $body): void {
    $kong = MockKong::queue(MockKong::raw(500, $body));

    $e = expectKongError($kong, ServerException::class, 500, null);

    expect($e->details)->toBe([])
        ->and($e->getMessage())->toBe('GET /services/svc failed with HTTP 500.');
})->with(['empty' => [''], 'html' => ['<h1>oops</h1>'], 'scalar' => ['"text"']]);

it('ignores a non-string message in the error body', function (): void {
    $kong = MockKong::queue(MockKong::json(400, ['message' => ['field' => 'bad'], 'name' => 'schema violation']));

    $e = expectKongError($kong, ValidationException::class, 400, null);

    expect($e->details)->toBe(['message' => ['field' => 'bad'], 'name' => 'schema violation']);
});

it('maps a connection failure to TransportException without a status', function (): void {
    $kong = MockKong::queue(new ConnectException('Could not resolve host', new Request('GET', 'http://kong.test/services/svc')));

    $e = expectKongError($kong, TransportException::class, null, null);

    expect($e->getPrevious())->toBeInstanceOf(ConnectException::class)
        ->and($e->getMessage())->toContain('failed before a response was received: Could not resolve host')
        ->and($e->getCode())->toBe(0);
});

it('maps a Guzzle RequestException without a response to TransportException', function (): void {
    $kong = MockKong::queue(new RequestException('TLS handshake failed', new Request('GET', 'http://kong.test/')));

    expectKongError($kong, TransportException::class, null, null);
});

it('maps a Guzzle ClientException (http_errors on) by its response status', function (): void {
    $request = new Request('GET', 'http://kong.test/services/svc');
    $kong = MockKong::queue(new ClientException('Not found', $request, MockKong::json(404, ['message' => 'Not found'])));

    expectKongError($kong, NotFoundException::class, 404, 'Not found');
});

it('wraps any PSR-18 client exception, not only Guzzle ones', function (): void {
    $failing = new class () implements ClientInterface {
        public function sendRequest(RequestInterface $request): ResponseInterface
        {
            throw new class ('network down', $request) extends RuntimeException implements NetworkExceptionInterface {
                public function __construct(string $message, private readonly RequestInterface $request)
                {
                    parent::__construct($message);
                }

                public function getRequest(): RequestInterface
                {
                    return $this->request;
                }
            };
        }
    };
    $client = new Aybarsm\Kong\AdminApi\KongClient(new ClientConfig(MockKong::BASE_URI), $failing);

    expect(fn (): Service => $client->services()->get('svc'))
        ->toThrow(TransportException::class, 'network down');
});

it('rejects a 2xx body that is not valid JSON', function (): void {
    $kong = MockKong::queue(MockKong::raw(200, 'not json'));

    $e = expectKongError($kong, UnexpectedResponseException::class, 200, null);

    expect($e->getPrevious())->toBeInstanceOf(JsonException::class);
});

it('rejects a 2xx JSON body that is a scalar', function (): void {
    $kong = MockKong::queue(MockKong::raw(200, '42'));

    expectKongError($kong, UnexpectedResponseException::class, 200, null);
});

it('rejects redirects and informational statuses', function (int $status): void {
    $kong = MockKong::queue(MockKong::raw($status));

    $e = expectKongError($kong, UnexpectedResponseException::class, $status, null);

    expect($e->getMessage())->toBe(sprintf('GET /services/svc returned unexpected HTTP %d.', $status));
})->with([101, 301, 304]);

it('sends the admin token, default headers and Accept on every request', function (): void {
    $kong = MockKong::withConfig(
        new ClientConfig(MockKong::BASE_URI, adminToken: 's3cr3t', headers: ['X-Request-Source' => 'tests']),
        MockKong::json(200, Fixture::get('service')),
    );

    $kong->client->services()->get('svc');
    $request = $kong->lastRequest();

    expect($request->getHeaderLine('Kong-Admin-Token'))->toBe('s3cr3t')
        ->and($request->getHeaderLine('X-Request-Source'))->toBe('tests')
        ->and($request->getHeaderLine('Accept'))->toBe('application/json')
        ->and($request->hasHeader('Content-Type'))->toBeFalse();
});

it('does not send a token header when none is configured', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('service')));

    $kong->client->services()->get('svc');

    expect($kong->lastRequest()->hasHeader('Kong-Admin-Token'))->toBeFalse();
});

it('never puts the admin token into exception messages', function (): void {
    $kong = MockKong::withConfig(
        new ClientConfig(MockKong::BASE_URI, adminToken: 'tok-123'),
        MockKong::json(401, ['message' => 'Invalid credentials. Token or User credentials required', 'status' => 401]),
    );

    $e = expectKongError($kong, UnauthorizedException::class, 401, 'Invalid credentials. Token or User credentials required');

    expect($e->getMessage())->not->toContain('tok-123');
});

it('keeps a base path from the base URI', function (): void {
    $kong = MockKong::withConfig(new ClientConfig('https://admin.example.com:8444/kong/'), MockKong::json(200, Fixture::get('service')));

    $kong->client->services()->get('svc');

    expect((string) $kong->lastRequest()->getUri())->toBe('https://admin.example.com:8444/kong/services/svc');
});

it('rejects a body that cannot be JSON-encoded', function (): void {
    $kong = MockKong::queue();

    expect(fn (): Service => $kong->client->services()->create(['name' => "\xB1\x31"]))
        ->toThrow(InvalidArgumentException::class, 'Request body for POST /services is not JSON-encodable.')
        ->and($kong->requestCount())->toBe(0);
});

it('builds paths per operation scope', function (?string $workspace, OperationScope $scope, string $expected): void {
    $transport = new Transport(new Client(), new HttpFactory(), new ClientConfig(workspace: $workspace));

    expect($transport->path($scope, 'rbac', 'users', 'id 1'))->toBe($expected);
})->with([
    'both, no workspace' => [null, OperationScope::Both, '/rbac/users/id%201'],
    'both, workspace' => ['ws', OperationScope::Both, '/ws/rbac/users/id%201'],
    'global, workspace ignored' => ['ws', OperationScope::GlobalOnly, '/rbac/users/id%201'],
    'workspace-only, spec default' => [null, OperationScope::WorkspaceOnly, '/default/rbac/users/id%201'],
    'workspace-only, workspace' => ['team/a', OperationScope::WorkspaceOnly, '/team%2Fa/rbac/users/id%201'],
]);

it('encodes query values: booleans as true/false and nulls omitted', function (): void {
    $transport = new Transport(
        new Client(['handler' => GuzzleHttp\HandlerStack::create(new GuzzleHttp\Handler\MockHandler([
            static function (RequestInterface $request): Response {
                expect($request->getUri()->getQuery())->toBe('list_consumers=true&flag=false&size=5&tags=a%2Cb');

                return MockKong::json(200, []);
            },
        ]))]),
        new HttpFactory(),
        new ClientConfig(MockKong::BASE_URI),
    );

    expect($transport->json('GET', '/x', ['list_consumers' => true, 'flag' => false, 'skip' => null, 'size' => 5, 'tags' => 'a,b']))
        ->toBe([]);
});
