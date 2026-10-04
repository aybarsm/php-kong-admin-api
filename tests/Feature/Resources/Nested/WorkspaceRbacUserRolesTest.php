<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Enums\RbacRoleSource;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Models\RbacUserRole;
use Aybarsm\Kong\AdminApi\Models\RbacUserRoleInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Resources\Nested\WorkspaceRbacUserRoles;
use Aybarsm\Kong\AdminApi\Resources\WorkspaceRbacUsers;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(WorkspaceRbacUserRoles::class, WorkspaceRbacUsers::class);

it('sends every operation under the default workspace when none is set', function (string $operation, string $method, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => [Fixture::get('rbac_user_role')]]),
        'add' => MockKong::json(201, Fixture::get('rbac_user_role')),
        default => MockKong::raw(204),
    });
    $roles = $kong->client->workspaceRbacUsers()->roles('u 1');

    match ($operation) {
        'list' => $roles->list(),
        'add' => $roles->add(new RbacUserRoleInput(role: 'r1', roleSource: RbacRoleSource::Local)),
        'remove' => $roles->remove(['role' => ['id' => 'r1']]),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/default/rbac/users/u%201/roles')
        ->and((string) $kong->lastRequest()->getBody())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', null],
    'add' => ['add', 'POST', '{"role":{"id":"r1"},"role_source":"local"}'],
    'remove (DELETE with a JSON body)' => ['remove', 'DELETE', '{"role":{"id":"r1"}}'],
]);

it('uses the configured workspace and walks pages', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('rbac_user_role')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('rbac_user_role')]]),
    );

    $all = iterator_to_array($kong->client->inWorkspace('team-a')->workspaceRbacUsers()->roles('u')->all(new ListOptions(size: 1)));

    expect($all)->toEqual([RbacUserRole::fromArray(Fixture::get('rbac_user_role')), RbacUserRole::fromArray(Fixture::get('rbac_user_role'))])
        ->and($kong->requestAt(1)->getUri()->getPath())->toBe('/team-a/rbac/users/u/roles')
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p2']);
});

it('rejects an empty user ID', function (): void {
    expect(fn (): WorkspaceRbacUserRoles => MockKong::queue()->client->workspaceRbacUsers()->roles(''))
        ->toThrow(InvalidArgumentException::class, 'RBAC user ID must not be empty.');
});
