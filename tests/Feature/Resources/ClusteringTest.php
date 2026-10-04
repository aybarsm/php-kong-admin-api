<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Exceptions\ValidationException;
use Aybarsm\Kong\AdminApi\Models\DataPlane;
use Aybarsm\Kong\AdminApi\Models\DataPlaneStatus;
use Aybarsm\Kong\AdminApi\Resources\Clustering;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(Clustering::class);

it('lists connected data planes', function (): void {
    $kong = MockKong::queue(MockKong::json(200, ['data' => [Fixture::get('data_plane')]]));

    $planes = $kong->client->inWorkspace('team-a')->clustering()->dataPlanes();

    expect($planes)->toEqual([DataPlane::fromArray(Fixture::get('data_plane'))])
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/clustering/data-planes');
});

it('maps the status map keyed by node ID', function (): void {
    $kong = MockKong::queue(MockKong::json(200, ['node-1' => Fixture::get('data_plane_status'), 'node-2' => Fixture::get('data_plane_status')]));

    $status = $kong->client->clustering()->status();

    expect(array_keys($status))->toBe(['node-1', 'node-2'])
        ->and($status['node-1'])->toEqual(DataPlaneStatus::fromArray(Fixture::get('data_plane_status')))
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/clustering/status');
});

it('maps an empty status object to an empty map and rejects a list', function (): void {
    $kong = MockKong::queue(MockKong::raw(200, '{}'), MockKong::json(200, [['ip' => 'x']]));

    expect($kong->client->clustering()->status())->toBe([])
        ->and(fn (): array => $kong->client->clustering()->status())->toThrow(UnexpectedResponseException::class);
});

it('maps the not-a-control-plane 400', function (): void {
    $kong = MockKong::queue(MockKong::json(400, ['message' => 'This endpoint is only available when Kong is running as a control plane for the cluster.']));

    expect(fn (): array => $kong->client->clustering()->dataPlanes())->toThrow(ValidationException::class, 'control plane');
});
