<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Models\RbacRoleEndpoint;
use Aybarsm\Kong\AdminApi\Models\RbacRoleEndpointInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Resources\Nested\WorkspaceRbacRoleEndpoints;
use Aybarsm\Kong\AdminApi\Resources\WorkspaceRbacRoles;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(WorkspaceRbacRoleEndpoints::class, WorkspaceRbacRoles::class);

it('lists, walks and creates endpoint permissions under the workspace', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('rbac_role_endpoint')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('rbac_role_endpoint')]]),
        MockKong::json(201, Fixture::get('rbac_role_endpoint')),
    );
    $endpoints = $kong->client->workspaceRbacRoles()->endpoints('r 1');

    $page = $endpoints->list(new ListOptions(size: 1));
    $rest = iterator_to_array($endpoints->all(new ListOptions(size: 1, offset: 'p2')));
    $created = $endpoints->create(new RbacRoleEndpointInput(actions: ['read'], endpoint: '/services'));

    expect($page->data)->toEqual([RbacRoleEndpoint::fromArray(Fixture::get('rbac_role_endpoint'))])
        ->and($rest)->toHaveCount(1)
        ->and($kong->requestAt(0)->getUri()->getPath())->toBe('/default/rbac/roles/r%201/endpoints')
        ->and($kong->lastRequest()->getMethod())->toBe('POST')
        ->and($kong->lastJsonBody())->toBe(['actions' => ['read'], 'endpoint' => '/services'])
        ->and($created)->toEqual(RbacRoleEndpoint::fromArray(Fixture::get('rbac_role_endpoint')));
});

it('uses the configured workspace', function (): void {
    $kong = MockKong::queue(MockKong::json(200, ['data' => []]));

    $kong->client->inWorkspace('team-a')->workspaceRbacRoles()->endpoints('r')->list();

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/team-a/rbac/roles/r/endpoints');
});

it('rejects an empty role ID', function (): void {
    expect(fn (): WorkspaceRbacRoleEndpoints => MockKong::queue()->client->workspaceRbacRoles()->endpoints(''))
        ->toThrow(InvalidArgumentException::class, 'RBAC role ID must not be empty.');
});
