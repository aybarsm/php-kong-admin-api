<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Models\AuditObject;
use Aybarsm\Kong\AdminApi\Models\AuditRequest;
use Aybarsm\Kong\AdminApi\Resources\AuditLogs;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(AuditLogs::class);

it('lists audit objects and requests from bare arrays, never workspace-prefixed', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, [Fixture::get('audit_object')]),
        MockKong::json(200, [Fixture::get('audit_request'), Fixture::get('audit_request')]),
    );
    $audit = $kong->client->inWorkspace('team-a')->auditLogs();

    expect($audit->objects())->toEqual([AuditObject::fromArray(Fixture::get('audit_object'))])
        ->and($kong->requestAt(0)->getUri()->getPath())->toBe('/audit/objects')
        ->and($kong->queryAt(0))->toBe([])
        ->and($audit->requests())->toHaveCount(2)
        ->and($kong->requestAt(1)->getUri()->getPath())->toBe('/audit/requests');
});

it('sends before and after in RFC 3339', function (): void {
    $kong = MockKong::queue(MockKong::json(200, []), MockKong::json(200, []));
    $before = new DateTimeImmutable('2026-10-04T12:00:00+02:00');
    $after = new DateTimeImmutable('2026-10-01T00:00:00Z');

    $kong->client->auditLogs()->requests($before, $after);
    $kong->client->auditLogs()->objects(after: $after);

    expect($kong->queryAt(0))->toBe(['before' => '2026-10-04T12:00:00+02:00', 'after' => '2026-10-01T00:00:00+00:00'])
        ->and($kong->queryAt(1))->toBe(['after' => '2026-10-01T00:00:00+00:00']);
});

it('maps a request record', function (): void {
    $kong = MockKong::queue(MockKong::json(200, [Fixture::get('audit_request')]));

    expect($kong->client->auditLogs()->requests())->toEqual([AuditRequest::fromArray(Fixture::get('audit_request'))]);
});
