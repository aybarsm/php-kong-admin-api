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
    'key without cert' => [fn (): ClientConfig => new ClientConfig(clientKey: __FILE__), 'clientKey and clientKeyPassphrase require clientCert'],
    'passphrase without cert' => [fn (): ClientConfig => new ClientConfig(clientKeyPassphrase: 'pw'), 'clientKey and clientKeyPassphrase require clientCert'],
    'empty passphrase' => [fn (): ClientConfig => new ClientConfig(clientCert: __FILE__, clientKeyPassphrase: ''), 'clientKeyPassphrase must not be empty'],
    'missing cert' => [fn (): ClientConfig => new ClientConfig(clientCert: '/no/such/cert.pem'), 'clientCert must be a readable file: "/no/such/cert.pem"'],
    'cert is a directory' => [fn (): ClientConfig => new ClientConfig(clientCert: __DIR__), 'clientCert must be a readable file'],
    'empty cert path' => [fn (): ClientConfig => new ClientConfig(clientCert: ''), 'clientCert must be a readable file'],
    'missing key' => [fn (): ClientConfig => new ClientConfig(clientCert: __FILE__, clientKey: '/no/such/key.pem'), 'clientKey must be a readable file: "/no/such/key.pem"'],
    'missing CA bundle' => [fn (): ClientConfig => new ClientConfig(verify: '/no/such/ca.pem'), 'verify must be a boolean or a readable CA bundle file or directory: "/no/such/ca.pem"'],
    'empty CA bundle path' => [fn (): ClientConfig => new ClientConfig(verify: ''), 'verify must be a boolean or a readable CA bundle'],
]);

it('rejects TLS files it cannot read', function (): void {
    $file = tempnam(sys_get_temp_dir(), 'kong-tls-');
    $dir = sys_get_temp_dir() . '/kong-tls-dir-' . bin2hex(random_bytes(4));
    if ($file === false || !mkdir($dir)) {
        throw new RuntimeException('Cannot create temporary TLS paths');
    }
    chmod($file, 0o000);
    chmod($dir, 0o000);

    try {
        expect(fn (): ClientConfig => new ClientConfig(clientCert: $file))->toThrow(InvalidArgumentException::class, 'clientCert must be a readable file')
            ->and(fn (): ClientConfig => new ClientConfig(clientCert: __FILE__, clientKey: $file))->toThrow(InvalidArgumentException::class, 'clientKey must be a readable file')
            ->and(fn (): ClientConfig => new ClientConfig(verify: $file))->toThrow(InvalidArgumentException::class, 'verify must be a boolean')
            ->and(fn (): ClientConfig => new ClientConfig(verify: $dir))->toThrow(InvalidArgumentException::class, 'verify must be a boolean');
    } finally {
        chmod($file, 0o600);
        chmod($dir, 0o700);
        unlink($file);
        rmdir($dir);
    }
})->skip(fn (): bool => function_exists('posix_geteuid') && posix_geteuid() === 0, 'root can read any file');

it('accepts a client certificate, key, passphrase and CA bundle file or directory', function (): void {
    $config = new ClientConfig('https://kong.test/', clientCert: __FILE__, clientKey: __FILE__, clientKeyPassphrase: 'pw', verify: __FILE__);
    $directory = new ClientConfig('https://kong.test/', clientCert: __FILE__, verify: __DIR__);

    expect($config->clientCert)->toBe(__FILE__)
        ->and($config->clientKey)->toBe(__FILE__)
        ->and($config->clientKeyPassphrase)->toBe('pw')
        ->and($config->verify)->toBe(__FILE__)
        ->and($directory->verify)->toBe(__DIR__)
        ->and($directory->clientKey)->toBeNull()
        ->and((new ClientConfig(verify: false))->verify)->toBeFalse()
        ->and((new ClientConfig())->verify)->toBeTrue();
});

it('accepts https, zero timeouts and token-safe headers', function (): void {
    $config = new ClientConfig('HTTPS://kong.example.com:8444', 'tok', 'ws', 0.0, 0.0, ['X-Trace' => '1']);

    expect($config->timeout)->toBe(0.0)
        ->and($config->workspace)->toBe('ws');
});

it('returns a copy scoped to another workspace', function (): void {
    $config = new ClientConfig('https://kong.test/', 'tok', null, 2.5, 1.0, ['X-A' => 'b'], __FILE__, __FILE__, 'pw', __DIR__);
    $scoped = $config->withWorkspace('team-a');

    expect($scoped)->not->toBe($config)
        ->and($scoped->workspace)->toBe('team-a')
        ->and($scoped->baseUri)->toBe('https://kong.test/')
        ->and($scoped->adminToken)->toBe('tok')
        ->and($scoped->timeout)->toBe(2.5)
        ->and($scoped->connectTimeout)->toBe(1.0)
        ->and($scoped->headers)->toBe(['X-A' => 'b'])
        ->and($scoped->clientCert)->toBe(__FILE__)
        ->and($scoped->clientKey)->toBe(__FILE__)
        ->and($scoped->clientKeyPassphrase)->toBe('pw')
        ->and($scoped->verify)->toBe(__DIR__)
        ->and($config->workspace)->toBeNull();
});

it('redacts the token, header values and key passphrase in debug output', function (): void {
    $config = new ClientConfig(
        adminToken: 'super-secret',
        headers: ['X-Api-Key' => 'also-secret'],
        clientCert: __FILE__,
        clientKey: __FILE__,
        clientKeyPassphrase: 'passphrase-secret',
        verify: false,
    );

    expect($config->__debugInfo())->toBe([
        'baseUri' => 'http://localhost:8001/',
        'adminToken' => '***',
        'workspace' => null,
        'timeout' => null,
        'connectTimeout' => null,
        'headers' => ['X-Api-Key'],
        'clientCert' => __FILE__,
        'clientKey' => __FILE__,
        'clientKeyPassphrase' => '***',
        'verify' => false,
    ])->and(print_r($config, true))->not->toContain('secret');
});

it('reports a missing token and passphrase as null in debug output', function (): void {
    $debug = (new ClientConfig())->__debugInfo();

    expect($debug['adminToken'])->toBeNull()
        ->and($debug['clientKeyPassphrase'])->toBeNull()
        ->and($debug['verify'])->toBeTrue();
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
