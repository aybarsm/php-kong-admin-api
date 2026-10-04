<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\ServerException;
use Aybarsm\Kong\AdminApi\Exceptions\UnauthorizedException;
use Aybarsm\Kong\AdminApi\Exceptions\ValidationException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\DnsStatus;
use Aybarsm\Kong\AdminApi\Models\FipsStatus;
use Aybarsm\Kong\AdminApi\Models\KongInfo;
use Aybarsm\Kong\AdminApi\Models\NodeStatus;
use Aybarsm\Kong\AdminApi\Models\Timers;
use Aybarsm\Kong\AdminApi\Resources\Information;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;
use GuzzleHttp\Psr7\Response;

covers(Information::class, Transport::class);

it('maps every GET to its spec DTO on the spec path, never workspace-prefixed', function (string $operation, string $path, string $fixture, string $class): void {
    /** @var class-string<KongInfo|NodeStatus|DnsStatus|Timers|FipsStatus> $class */
    $kong = MockKong::queue(MockKong::json(200, Fixture::get($fixture)));
    $info = $kong->client->inWorkspace('team-a')->information();

    $result = match ($operation) {
        'info' => $info->info(),
        'status' => $info->status(),
        'dnsStatus' => $info->dnsStatus(),
        'timers' => $info->timers(),
        'fipsStatus' => $info->fipsStatus(),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe('GET')
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and($result)->toEqual($class::fromArray(Fixture::get($fixture)));
})->with([
    'info' => ['info', '/', 'kong_info', KongInfo::class],
    'status' => ['status', '/status', 'node_status', NodeStatus::class],
    'dns status' => ['dnsStatus', '/status/dns', 'dns_status', DnsStatus::class],
    'timers' => ['timers', '/timers', 'timers', Timers::class],
    'fips status' => ['fipsStatus', '/fips-status', 'fips_status', FipsStatus::class],
]);

it('unwraps the endpoint list', function (): void {
    $kong = MockKong::queue(MockKong::json(200, ['data' => ['/services', '/routes']]));

    expect($kong->client->information()->endpoints())->toBe(['/services', '/routes'])
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/endpoints');
});

it('maps the legacy DNS client 501 to ServerException', function (): void {
    $kong = MockKong::queue(MockKong::json(501, ['message' => 'legacy DNS client in use']));

    expect(fn (): DnsStatus => $kong->client->information()->dnsStatus())->toThrow(ServerException::class, 'legacy DNS client in use');
});

it('checks endpoint existence with HEAD: 204 is true, 404 is false', function (): void {
    $kong = MockKong::queue(MockKong::raw(204), MockKong::raw(404));
    $info = $kong->client->information();

    expect($info->endpointExists('services'))->toBeTrue()
        ->and($info->endpointExists('nope'))->toBeFalse()
        ->and($kong->requestAt(0)->getMethod())->toBe('HEAD')
        ->and($kong->requestAt(0)->getUri()->getPath())->toBe('/services')
        ->and($kong->requestAt(1)->getUri()->getPath())->toBe('/nope');
});

it('still raises other errors from HEAD', function (): void {
    $kong = MockKong::queue(MockKong::json(401, ['message' => 'Unauthorized', 'status' => 401]));

    expect(fn (): bool => $kong->client->information()->endpointExists('services'))->toThrow(UnauthorizedException::class);
});

it('reads allowed methods from the OPTIONS Allow header', function (): void {
    $kong = MockKong::queue(new Response(204, ['Allow' => ['GET, HEAD,OPTIONS', ' POST ,']]));

    $methods = $kong->client->information()->allowedMethods('services');

    expect($methods)->toBe(['GET', 'HEAD', 'OPTIONS', 'POST'])
        ->and($kong->lastRequest()->getMethod())->toBe('OPTIONS')
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/services');
});

it('returns no methods when the Allow header is missing, and maps 400', function (): void {
    $kong = MockKong::queue(MockKong::raw(204), MockKong::raw(400));

    expect($kong->client->information()->allowedMethods('services'))->toBe([])
        ->and(fn (): array => $kong->client->information()->allowedMethods('x'))->toThrow(ValidationException::class);
});

it('rejects an empty endpoint', function (): void {
    expect(fn (): bool => MockKong::queue()->client->information()->endpointExists(''))->toThrow(InvalidArgumentException::class);
});
