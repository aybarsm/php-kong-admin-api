<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Models\GroupRole;
use Aybarsm\Kong\AdminApi\Models\GroupRoleInput;
use Aybarsm\Kong\AdminApi\Resources\Groups;
use Aybarsm\Kong\AdminApi\Resources\Nested\GroupRoles;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(GroupRoles::class, Groups::class);

it('sends every operation to the spec path, never workspace-prefixed', function (string $operation, string $method, string $query, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => [Fixture::get('group_role')]]),
        'add' => MockKong::json(201, Fixture::get('group_role')),
        default => MockKong::raw(204),
    });
    $roles = $kong->client->inWorkspace('team-a')->groups()->roles('g 1');

    match ($operation) {
        'list' => $roles->list(),
        'add' => $roles->add(new GroupRoleInput(roleId: 'r1', workspace: 'w1')),
        'remove' => $roles->remove('r1', 'w1'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe('/groups/g%201/roles')
        ->and($kong->lastRequest()->getUri()->getQuery())->toBe($query)
        ->and((string) $kong->lastRequest()->getBody())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', '', null],
    'add' => ['add', 'POST', '', '{"role_id":"r1","workspace":"w1"}'],
    'remove' => ['remove', 'DELETE', 'rbac_role_id=r1&workspace_id=w1', null],
]);

it('maps the {data} list and the created role', function (): void {
    $kong = MockKong::queue(MockKong::json(200, ['data' => [Fixture::get('group_role')]]), MockKong::json(201, Fixture::get('group_role')));
    $roles = $kong->client->groups()->roles('g');

    expect($roles->list())->toEqual([GroupRole::fromArray(Fixture::get('group_role'))])
        ->and($roles->add([]))->toEqual(GroupRole::fromArray(Fixture::get('group_role')));
});

it('rejects empty arguments', function (): void {
    $kong = MockKong::queue();

    expect(fn (): GroupRoles => $kong->client->groups()->roles(''))->toThrow(InvalidArgumentException::class, 'Group ID must not be empty.')
        ->and(fn () => $kong->client->groups()->roles('g')->remove('', 'w'))->toThrow(InvalidArgumentException::class)
        ->and($kong->requestCount())->toBe(0);
});
