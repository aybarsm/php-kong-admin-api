<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Enums\DeploymentType;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnauthorizedException;
use Aybarsm\Kong\AdminApi\Models\License;
use Aybarsm\Kong\AdminApi\Models\LicenseInput;
use Aybarsm\Kong\AdminApi\Models\LicenseReport;
use Aybarsm\Kong\AdminApi\Resources\Licenses;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(Licenses::class, LicenseInput::class);

it('sends every operation to the spec path and body, never workspace-prefixed', function (string $operation, string $method, string $path, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'delete' => MockKong::raw(204),
        'report' => MockKong::json(200, Fixture::get('license_report')),
        default => MockKong::json(200, Fixture::get('license')),
    });
    $licenses = $kong->client->inWorkspace('team-a')->licenses();

    match ($operation) {
        'list' => $licenses->list(),
        'create' => $licenses->create(new LicenseInput(key: '{"license":{}}')),
        'get' => $licenses->get('l 1'),
        'update' => $licenses->update('l 1', ['key' => 'k']),
        'upsert' => $licenses->upsert('l 1', new LicenseInput(id: 'l 1', key: 'k')),
        'delete' => $licenses->delete('l 1'),
        'report' => $licenses->report(),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and((string) $kong->lastRequest()->getBody())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', '/licenses', null],
    'create' => ['create', 'POST', '/licenses', '{"key":"{\"license\":{}}"}'],
    'get' => ['get', 'GET', '/licenses/l%201', null],
    'update' => ['update', 'PATCH', '/licenses/l%201', '{"key":"k"}'],
    'upsert' => ['upsert', 'PUT', '/licenses/l%201', '{"id":"l 1","key":"k"}'],
    'delete' => ['delete', 'DELETE', '/licenses/l%201', null],
    'report' => ['report', 'GET', '/license/report', null],
]);

it('maps the list response literally as a single License (spec-notes Q8) and the report', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('license')), MockKong::json(200, Fixture::get('license_report')));

    $license = $kong->client->licenses()->list();
    $report = $kong->client->licenses()->report();

    expect($license)->toEqual(License::fromArray(Fixture::get('license')))
        ->and($report)->toEqual(LicenseReport::fromArray(Fixture::get('license_report')))
        ->and($report->deploymentInfo?->type)->toBe(DeploymentType::Traditional);
});

it('maps the license-specific 401 body', function (): void {
    $kong = MockKong::queue(MockKong::json(401, ['message' => 'Unauthorized', 'status' => 401]));

    expect(fn (): License => $kong->client->licenses()->list())->toThrow(UnauthorizedException::class, 'Unauthorized');
});

it('rejects an empty license ID', function (): void {
    expect(fn (): License => MockKong::queue()->client->licenses()->get(''))->toThrow(InvalidArgumentException::class);
});
