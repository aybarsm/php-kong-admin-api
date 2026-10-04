<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\ConflictException;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Models\Admin;
use Aybarsm\Kong\AdminApi\Models\AdminCreationInput;
use Aybarsm\Kong\AdminApi\Models\AdminInput;
use Aybarsm\Kong\AdminApi\Models\AdminPasswordResetInput;
use Aybarsm\Kong\AdminApi\Models\AdminPasswordResetRequestInput;
use Aybarsm\Kong\AdminApi\Models\AdminRegistrationInput;
use Aybarsm\Kong\AdminApi\Models\AdminRoles;
use Aybarsm\Kong\AdminApi\Models\AdminRolesInput;
use Aybarsm\Kong\AdminApi\Models\AdminSummary;
use Aybarsm\Kong\AdminApi\Models\Workspace;
use Aybarsm\Kong\AdminApi\Resources\Admins;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(
    Admins::class,
    AdminCreationInput::class,
    AdminRegistrationInput::class,
    AdminPasswordResetRequestInput::class,
    AdminPasswordResetInput::class,
    AdminRolesInput::class,
);

it('sends every operation to the spec path and body, never workspace-prefixed', function (string $operation, string $method, string $path, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => [Fixture::get('admin_summary')], 'next' => null]),
        'get', 'update', 'upsert' => MockKong::json(200, Fixture::get('admin')),
        'roles' => MockKong::json(200, ['roles' => []]),
        'addRoles' => MockKong::json(201, Fixture::get('admin_roles')),
        'workspaces' => MockKong::json(200, Fixture::get('workspace')),
        'updateWorkspace' => MockKong::json(200, Fixture::get('admin_summary')),
        'create' => MockKong::raw(200),
        'register', 'requestPasswordReset' => MockKong::raw(201),
        'resetPassword' => MockKong::raw(200),
        default => MockKong::raw(204),
    });
    $admins = $kong->client->inWorkspace('team-a')->admins();

    match ($operation) {
        'list' => $admins->list(),
        'create' => $admins->create(new AdminCreationInput(email: 'a@example.com', username: 'alice')),
        'get' => $admins->get('a 1'),
        'update' => $admins->update('a 1', new AdminInput(email: 'b@example.com')),
        'upsert' => $admins->upsert('a 1', ['username' => 'alice']),
        'delete' => $admins->delete('a 1'),
        'register' => $admins->register(new AdminRegistrationInput(password: 'pw', token: 't', username: 'alice')),
        'requestPasswordReset' => $admins->requestPasswordReset(new AdminPasswordResetRequestInput(email: 'a@example.com')),
        'resetPassword' => $admins->resetPassword(new AdminPasswordResetInput(email: 'a@example.com', password: 'pw', token: 't')),
        'roles' => $admins->roles('alice'),
        'addRoles' => $admins->addRoles('alice', new AdminRolesInput(roles: 'read-only')),
        'removeRoles' => $admins->removeRoles('alice'),
        'workspaces' => $admins->workspaces('alice'),
        'updateWorkspace' => $admins->updateWorkspace('alice', 'team b'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and((string) $kong->lastRequest()->getBody())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', '/admins', null],
    'create' => ['create', 'POST', '/admins', '{"email":"a@example.com","username":"alice"}'],
    'get' => ['get', 'GET', '/admins/a%201', null],
    'update' => ['update', 'PATCH', '/admins/a%201', '{"email":"b@example.com"}'],
    'upsert' => ['upsert', 'PUT', '/admins/a%201', '{"username":"alice"}'],
    'delete' => ['delete', 'DELETE', '/admins/a%201', null],
    'register' => ['register', 'POST', '/admins/register', '{"password":"pw","token":"t","username":"alice"}'],
    'requestPasswordReset' => ['requestPasswordReset', 'POST', '/admins/password_resets', '{"email":"a@example.com"}'],
    'resetPassword' => ['resetPassword', 'PATCH', '/admins/password_resets', '{"email":"a@example.com","password":"pw","token":"t"}'],
    'roles' => ['roles', 'GET', '/admins/alice/roles', null],
    'addRoles' => ['addRoles', 'POST', '/admins/alice/roles', '{"roles":"read-only"}'],
    'removeRoles' => ['removeRoles', 'DELETE', '/admins/alice/roles', null],
    'workspaces' => ['workspaces', 'GET', '/admins/alice/workspaces', null],
    'updateWorkspace' => ['updateWorkspace', 'PATCH', '/admins/alice/workspaces/team%20b', null],
]);

it('maps every response to its spec DTO', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('admin_summary')], 'next' => '/admins?offset=x']),
        MockKong::json(200, Fixture::get('admin')),
        MockKong::json(201, Fixture::get('admin_roles')),
        MockKong::json(200, Fixture::get('workspace')),
        MockKong::json(200, Fixture::get('admin_summary')),
        MockKong::json(200, ['roles' => [['name' => 'read-only']]]),
    );
    $admins = $kong->client->admins();

    $page = $admins->list();

    expect($page->data)->toEqual([AdminSummary::fromArray(Fixture::get('admin_summary'))])
        ->and($page->next)->toBe('/admins?offset=x')
        ->and($admins->get('a'))->toEqual(Admin::fromArray(Fixture::get('admin')))
        ->and($admins->addRoles('a', []))->toEqual(AdminRoles::fromArray(Fixture::get('admin_roles')))
        ->and($admins->workspaces('a'))->toEqual(Workspace::fromArray(Fixture::get('workspace')))
        ->and($admins->updateWorkspace('a', 'w'))->toEqual(AdminSummary::fromArray(Fixture::get('admin_summary')))
        ->and($admins->roles('a'))->toBe(['roles' => [['name' => 'read-only']]]);
});

it('maps 409 on create to ConflictException', function (): void {
    $kong = MockKong::queue(MockKong::raw(409));

    expect(fn () => $kong->client->admins()->create(['username' => 'alice']))
        ->toThrow(ConflictException::class, 'POST /admins failed with HTTP 409.');
});

it('rejects empty IDs before sending anything', function (): void {
    $kong = MockKong::queue();
    $admins = $kong->client->admins();

    expect(fn (): Admin => $admins->get(''))->toThrow(InvalidArgumentException::class)
        ->and(fn (): AdminSummary => $admins->updateWorkspace('alice', ''))->toThrow(InvalidArgumentException::class)
        ->and($kong->requestCount())->toBe(0);
});
