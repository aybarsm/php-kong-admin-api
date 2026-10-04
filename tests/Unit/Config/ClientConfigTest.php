<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Config\ClientConfig;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongExceptionInterface;

covers(ClientConfig::class);

it('defaults to the spec server URL', function (): void {
    $config = new ClientConfig();

    expect($config->baseUri)->toBe('http://localhost:8001/')
        ->and($config->adminToken)->toBeNull()
        ->and($config->workspace)->toBeNull()
        ->and($config->headers)->toBe([]);
});

it('rejects invalid settings', function (callable $build, string $message): void {
    expect($build)->toThrow(InvalidArgumentException::class, $message);
})->with([
    'relative URI' => [fn (): ClientConfig => new ClientConfig('localhost:8001'), 'absolute http or https'],
    'ftp scheme' => [fn (): ClientConfig => new ClientConfig('ftp://kong'), 'absolute http or https'],
    'no host' => [fn (): ClientConfig => new ClientConfig('http:///path'), 'absolute http or https'],
    'query' => [fn (): ClientConfig => new ClientConfig('http://kong/?a=1'), 'query string or fragment'],
    'fragment' => [fn (): ClientConfig => new ClientConfig('http://kong/#x'), 'query string or fragment'],
    'empty token' => [fn (): ClientConfig => new ClientConfig(adminToken: ''), 'adminToken must not be empty'],
    'empty workspace' => [fn (): ClientConfig => new ClientConfig(workspace: ''), 'workspace must not be empty'],
    'negative timeout' => [fn (): ClientConfig => new ClientConfig(timeout: -1.0), 'Timeouts must be zero or greater'],
    'negative connect timeout' => [fn (): ClientConfig => new ClientConfig(connectTimeout: -0.5), 'Timeouts must be zero or greater'],
    'bad header name' => [fn (): ClientConfig => new ClientConfig(headers: ['Bad Header' => 'x']), 'Invalid header name "Bad Header"'],
]);

it('accepts https, zero timeouts and token-safe headers', function (): void {
    $config = new ClientConfig('HTTPS://kong.example.com:8444', 'tok', 'ws', 0.0, 0.0, ['X-Trace' => '1']);

    expect($config->timeout)->toBe(0.0)
        ->and($config->workspace)->toBe('ws');
});

it('returns a copy scoped to another workspace', function (): void {
    $config = new ClientConfig('https://kong.test/', 'tok', null, 2.5, 1.0, ['X-A' => 'b']);
    $scoped = $config->withWorkspace('team-a');

    expect($scoped)->not->toBe($config)
        ->and($scoped->workspace)->toBe('team-a')
        ->and($scoped->baseUri)->toBe('https://kong.test/')
        ->and($scoped->adminToken)->toBe('tok')
        ->and($scoped->timeout)->toBe(2.5)
        ->and($scoped->connectTimeout)->toBe(1.0)
        ->and($scoped->headers)->toBe(['X-A' => 'b'])
        ->and($config->workspace)->toBeNull();
});

it('redacts the token and header values in debug output', function (): void {
    $config = new ClientConfig(adminToken: 'super-secret', headers: ['X-Api-Key' => 'also-secret']);

    expect($config->__debugInfo())->toBe([
        'baseUri' => 'http://localhost:8001/',
        'adminToken' => '***',
        'workspace' => null,
        'timeout' => null,
        'connectTimeout' => null,
        'headers' => ['X-Api-Key'],
    ])->and(print_r($config, true))->not->toContain('secret');
});

it('reports a missing token as null in debug output', function (): void {
    expect((new ClientConfig())->__debugInfo()['adminToken'])->toBeNull();
});

it('throws exceptions catchable through the package marker interface', function (): void {
    try {
        new ClientConfig(workspace: '');
    } catch (Throwable $e) {
        expect($e)->toBeInstanceOf(KongExceptionInterface::class);

        return;
    }

    throw new RuntimeException('Expected an exception');
});
