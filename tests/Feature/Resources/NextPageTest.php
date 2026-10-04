<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Pagination\CountedPage;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Pagination\TagFilter;
use Aybarsm\Kong\AdminApi\Resources\Nested\ConsumerConsumerGroups;
use Aybarsm\Kong\AdminApi\Resources\Nested\ConsumerGroupConsumers;
use Aybarsm\Kong\AdminApi\Resources\Nested\PartialLinks;
use Aybarsm\Kong\AdminApi\Resources\Nested\ServiceRoutes;
use Aybarsm\Kong\AdminApi\Resources\Nested\WorkspaceRbacRoleEndpoints;
use Aybarsm\Kong\AdminApi\Resources\Nested\WorkspaceRbacUserRoles;
use Aybarsm\Kong\AdminApi\Resources\Partials;
use Aybarsm\Kong\AdminApi\Resources\Routes;
use Aybarsm\Kong\AdminApi\Resources\Services;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

/*
 * nextPage() on the hand-written paginated resources (generated ones are covered by their own feature tests):
 * it repeats list() with the page's `offset`, keeps filters and workspace, and stops after the last page
 * without a request (spec-notes Q6).
 */

covers(
    Services::class,
    Routes::class,
    ServiceRoutes::class,
    Partials::class,
    PartialLinks::class,
    ConsumerGroupConsumers::class,
    ConsumerConsumerGroups::class,
    WorkspaceRbacUserRoles::class,
    WorkspaceRbacRoleEndpoints::class,
);

/**
 * @return array{MockKong, ListOptions}
 */
function nextPageSetup(string $fixture): array
{
    return [
        MockKong::queue(
            MockKong::json(200, ['data' => [Fixture::get($fixture)], 'offset' => 'p2']),
            MockKong::json(200, ['data' => [Fixture::get($fixture)]]),
        ),
        new ListOptions(size: 1, tags: TagFilter::anyOf('a', 'b')),
    ];
}

/**
 * @param Page<mixed>|CountedPage<mixed>|null $next
 */
function expectNextPage(MockKong $kong, Page|CountedPage|null $next, string $path): void
{
    expect($next?->data)->toHaveCount(1)
        ->and($next?->offset)->toBeNull()
        ->and($kong->requestAt(1)->getUri()->getPath())->toBe($path)
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p2', 'tags' => 'a/b']);
}

it('pages Services and stops after the last page', function (): void {
    [$kong, $options] = nextPageSetup('service');
    $services = $kong->client->inWorkspace('team-a')->services();

    $next = $services->nextPage($services->list($options), $options);

    expectNextPage($kong, $next, '/team-a/services');
    expect($next === null ? 'none' : $services->nextPage($next, $options))->toBeNull()
        ->and($kong->requestCount())->toBe(2);
});

it('pages Routes and a Service\'s routes', function (): void {
    [$kong, $options] = nextPageSetup('route_json');
    $routes = $kong->client->routes();
    expectNextPage($kong, $routes->nextPage($routes->list($options), $options), '/routes');

    [$kong, $options] = nextPageSetup('route_expression');
    $nested = $kong->client->services()->routes('svc');
    expectNextPage($kong, $nested->nextPage($nested->list($options), $options), '/services/svc/routes');
});

it('pages Partials and a Partial\'s links (counted page)', function (): void {
    [$kong, $options] = nextPageSetup('partial_redis_ce');
    $partials = $kong->client->partials();
    expectNextPage($kong, $partials->nextPage($partials->list($options), $options), '/partials');

    [$kong, $options] = nextPageSetup('partial_link');
    $links = $kong->client->partials()->links('p1');
    $next = $links->nextPage($links->list($options), $options);
    expectNextPage($kong, $next, '/partials/p1/links');
    expect($next)->toBeInstanceOf(CountedPage::class);
});

it('pages consumer-group memberships from both sides', function (): void {
    [$kong, $options] = nextPageSetup('consumer');
    $members = $kong->client->consumerGroups()->consumers('gold');
    expectNextPage($kong, $members->nextPage($members->list($options), $options), '/consumer_groups/gold/consumers');

    [$kong, $options] = nextPageSetup('consumer_group');
    $groups = $kong->client->consumers()->consumerGroups('alice');
    expectNextPage($kong, $groups->nextPage($groups->list($options), $options), '/consumers/alice/consumer_groups');
});

it('pages workspace-only RBAC user roles and role endpoints', function (): void {
    [$kong, $options] = nextPageSetup('rbac_user_role');
    $roles = $kong->client->workspaceRbacUsers()->roles('u1');
    expectNextPage($kong, $roles->nextPage($roles->list($options), $options), '/default/rbac/users/u1/roles');

    [$kong, $options] = nextPageSetup('rbac_role_endpoint');
    $endpoints = $kong->client->inWorkspace('team-a')->workspaceRbacRoles()->endpoints('r1');
    expectNextPage($kong, $endpoints->nextPage($endpoints->list($options), $options), '/team-a/rbac/roles/r1/endpoints');
});

it('works without options and returns null for a page without offset', function (): void {
    $kong = MockKong::queue(MockKong::json(200, ['data' => []]));

    expect($kong->client->services()->nextPage(new Page([], 'o1')))->toBeInstanceOf(Page::class)
        ->and($kong->lastQuery())->toBe(['offset' => 'o1'])
        ->and($kong->client->services()->nextPage(new Page([])))->toBeNull()
        ->and($kong->requestCount())->toBe(1);
});
