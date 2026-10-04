<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Config\ClientConfig;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\KongClient;
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;
use Aybarsm\Kong\AdminApi\Resources\Routes;
use Aybarsm\Kong\AdminApi\Resources\Services;

covers(KongClient::class);

it('builds a default Guzzle client and exposes its configuration', function (): void {
    $config = new ClientConfig('http://kong.test/', timeout: 5.0, connectTimeout: 1.0);
    $client = new KongClient($config);

    expect($client->config())->toBe($config)
        ->and($client->services())->toBeInstanceOf(Services::class)
        ->and($client->routes())->toBeInstanceOf(Routes::class);
});

it('works with no arguments using the spec defaults', function (): void {
    expect((new KongClient())->config()->baseUri)->toBe('http://localhost:8001/');
});

it('is immutable when switching workspaces', function (): void {
    $client = new KongClient(new ClientConfig('http://kong.test/'));
    $scoped = $client->inWorkspace('team-a');

    expect($scoped)->not->toBe($client)
        ->and($scoped->config()->workspace)->toBe('team-a')
        ->and($client->config()->workspace)->toBeNull()
        ->and($scoped->withoutWorkspace()->config()->workspace)->toBeNull();
});

it('rejects an empty workspace name', function (): void {
    expect(fn (): KongClient => (new KongClient())->inWorkspace(''))->toThrow(InvalidArgumentException::class);
});

it('returns a fresh resource from every accessor, keeping the workspace', function (): void {
    $client = new KongClient(new ClientConfig('http://kong.test/'));
    $checked = 0;

    foreach ((new ReflectionClass(KongClient::class))->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
        $return = $method->getReturnType();
        if (!$return instanceof ReflectionNamedType || !is_subclass_of($return->getName(), AbstractResource::class) || $method->getNumberOfParameters() > 0) {
            continue;
        }

        $resource = $method->invoke($client);
        expect($resource)->toBeInstanceOf($return->getName())
            ->and($method->invoke($client))->not->toBe($resource);
        $checked++;
    }

    expect($checked)->toBeGreaterThanOrEqual(13);
});
