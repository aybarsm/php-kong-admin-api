<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\KongClient;
use Aybarsm\Kong\AdminApi\Models\RbacUserGroup;
use Aybarsm\Kong\AdminApi\Models\RbacUserGroupInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\TagFilter;
use Aybarsm\Kong\AdminApi\Resources\RbacUserGroups;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(RbacUserGroups::class);

function rbacUserGroupsUnderTest(KongClient $client): RbacUserGroups
{
    return $client->rbacUserGroups();
}

it('sends every operation to the spec path', function (string $operation, string $method, string $path, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => [Fixture::get('rbac_user_group')]]),
        'delete' => MockKong::raw(204),
        default => MockKong::json(200, Fixture::get('rbac_user_group')),
    });
    $resource = rbacUserGroupsUnderTest($kong->client);

    match ($operation) {
        'list' => $resource->list(),
        'get' => $resource->get('item 1'),
        'create' => $resource->create(['group' => 'value']),
        'update' => $resource->update('item 1', ['group' => 'value']),
        'upsert' => $resource->upsert('item 1', new RbacUserGroupInput()),
        'delete' => $resource->delete('item 1'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and($kong->lastRequest()->getBody()->__toString())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', '/rbac_user_groups', null],
    'get' => ['get', 'GET', '/rbac_user_groups/item%201', null],
    'create' => ['create', 'POST', '/rbac_user_groups', '{"group":"value"}'],
    'update' => ['update', 'PATCH', '/rbac_user_groups/item%201', '{"group":"value"}'],
    'upsert' => ['upsert', 'PUT', '/rbac_user_groups/item%201', '{}'],
    'delete' => ['delete', 'DELETE', '/rbac_user_groups/item%201', null],
]);

it('lists one page with options and walks every page', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('rbac_user_group')], 'offset' => 'p1']),
        MockKong::json(200, ['data' => [Fixture::get('rbac_user_group')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('rbac_user_group')]]),
    );
    $resource = rbacUserGroupsUnderTest($kong->client);

    $page = $resource->list(new ListOptions(size: 1, tags: TagFilter::allOf('a', 'b')));
    $rest = iterator_to_array($resource->all(new ListOptions(size: 1, offset: 'p1')));

    expect($page->data)->toEqual([RbacUserGroup::fromArray(Fixture::get('rbac_user_group'))])
        ->and($page->offset)->toBe('p1')
        ->and($kong->queryAt(0))->toBe(['size' => '1', 'tags' => 'a,b'])
        ->and($rest)->toHaveCount(2)
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p1'])
        ->and($kong->queryAt(2))->toBe(['size' => '1', 'offset' => 'p2'])
        ->and($kong->requestAt(2)->getUri()->getPath())->toBe('/rbac_user_groups');
});

it('maps the response to RbacUserGroup', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('rbac_user_group')));

    expect(rbacUserGroupsUnderTest($kong->client)->get('x'))->toEqual(RbacUserGroup::fromArray(Fixture::get('rbac_user_group')));
});

it('never prefixes a workspace on these global-only paths', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('rbac_user_group')));

    rbacUserGroupsUnderTest($kong->client->inWorkspace('team-a'))->get('x');

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/rbac_user_groups/x');
});

it('rejects an empty ID before sending anything', function (): void {
    $kong = MockKong::queue();

    expect(fn (): RbacUserGroup => rbacUserGroupsUnderTest($kong->client)->get(''))->toThrow(InvalidArgumentException::class)
        ->and($kong->requestCount())->toBe(0);
});

it('maps 404 to NotFoundException', function (): void {
    $kong = MockKong::queue(MockKong::raw(404));

    expect(fn (): RbacUserGroup => rbacUserGroupsUnderTest($kong->client)->get('missing'))
        ->toThrow(NotFoundException::class, 'GET /rbac_user_groups/missing failed with HTTP 404.');
});
