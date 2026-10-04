<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Enums\LogLevel;
use Aybarsm\Kong\AdminApi\Models\NodeLogLevel;
use Aybarsm\Kong\AdminApi\Resources\Debug;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(Debug::class);

it('sends every operation to the spec path, never workspace-prefixed', function (string $operation, string $method, string $path): void {
    $kong = MockKong::queue(match ($operation) {
        'nodeLogLevel', 'setNodeLogLevel' => MockKong::json(200, Fixture::get('node_log_level')),
        default => MockKong::raw(200),
    });
    $debug = $kong->client->inWorkspace('team-a')->debug();

    match ($operation) {
        'nodeLogLevel' => $debug->nodeLogLevel(),
        'setNodeLogLevel' => $debug->setNodeLogLevel(LogLevel::Debug),
        'setClusterLogLevel' => $debug->setClusterLogLevel(LogLevel::Warn),
        'setControlPlanesLogLevel' => $debug->setControlPlanesLogLevel(LogLevel::Crit),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and((string) $kong->lastRequest()->getBody())->toBe('');
})->with([
    'get node' => ['nodeLogLevel', 'GET', '/debug/node/log-level'],
    'set node' => ['setNodeLogLevel', 'PUT', '/debug/node/log-level/debug'],
    'set cluster' => ['setClusterLogLevel', 'PUT', '/debug/cluster/log-level/warn'],
    'set control planes' => ['setControlPlanesLogLevel', 'PUT', '/debug/cluster/control-planes-nodes/log-level/crit'],
]);

it('maps the log-level message', function (): void {
    $kong = MockKong::queue(MockKong::json(200, ['message' => 'log level: info']), MockKong::json(200, ['message' => 'log level changed']));

    expect($kong->client->debug()->nodeLogLevel())->toEqual(new NodeLogLevel('log level: info'))
        ->and($kong->client->debug()->setNodeLogLevel(LogLevel::Info))->toEqual(new NodeLogLevel('log level changed'));
});
