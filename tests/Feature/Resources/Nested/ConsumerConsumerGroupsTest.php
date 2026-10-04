<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\Models\ConsumerGroup;
use Aybarsm\Kong\AdminApi\Models\ConsumerGroupAssignmentInput;
use Aybarsm\Kong\AdminApi\Models\ConsumerMembership;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Resources\Consumers;
use Aybarsm\Kong\AdminApi\Resources\Nested\ConsumerConsumerGroups;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(ConsumerConsumerGroups::class, Consumers::class, ConsumerGroupAssignmentInput::class);

it('sends every operation to the spec path', function (string $operation, string $method, string $path, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => [Fixture::get('consumer_group')]]),
        'add' => MockKong::json(201, Fixture::get('consumer_membership')),
        default => MockKong::raw(204),
    });
    $groups = $kong->client->consumers()->consumerGroups('alice smith');

    match ($operation) {
        'list' => $groups->list(),
        'add' => $groups->add(new ConsumerGroupAssignmentInput(group: 'gold')),
        'removeAll' => $groups->removeAll(),
        'remove' => $groups->remove('gold'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and((string) $kong->lastRequest()->getBody())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', '/consumers/alice%20smith/consumer_groups', null],
    'add' => ['add', 'POST', '/consumers/alice%20smith/consumer_groups', '{"group":"gold"}'],
    'removeAll' => ['removeAll', 'DELETE', '/consumers/alice%20smith/consumer_groups', null],
    'remove' => ['remove', 'DELETE', '/consumers/alice%20smith/consumer_groups/gold', null],
]);

it('lists and walks the consumer groups of a consumer', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('consumer_group')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('consumer_group')]]),
    );

    $page = $kong->client->consumers()->consumerGroups('alice')->list(new ListOptions(size: 1));
    $all = iterator_to_array($kong->client->consumers()->consumerGroups('alice')->all(new ListOptions(size: 1, offset: 'p2')));

    expect($page->data)->toEqual([ConsumerGroup::fromArray(Fixture::get('consumer_group'))])
        ->and($all)->toHaveCount(1)
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p2']);
});

it('maps the membership returned when adding a consumer to a group', function (): void {
    $kong = MockKong::queue(MockKong::json(201, Fixture::get('consumer_membership')));

    expect($kong->client->consumers()->consumerGroups('alice')->add(['group' => 'gold']))
        ->toEqual(ConsumerMembership::fromArray(Fixture::get('consumer_membership')));
});

it('prefixes the workspace', function (): void {
    $kong = MockKong::queue(MockKong::json(200, ['data' => []]));

    $kong->client->inWorkspace('team-a')->consumers()->consumerGroups('alice')->list();

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/team-a/consumers/alice/consumer_groups');
});

it('rejects empty IDs before sending anything', function (): void {
    $kong = MockKong::queue();

    expect(fn (): ConsumerConsumerGroups => $kong->client->consumers()->consumerGroups(''))
        ->toThrow(InvalidArgumentException::class, 'Consumer ID or username must not be empty.')
        ->and(fn () => $kong->client->consumers()->consumerGroups('alice')->remove(''))->toThrow(InvalidArgumentException::class)
        ->and($kong->requestCount())->toBe(0);
});

it('maps 404 for an unknown consumer', function (): void {
    $kong = MockKong::queue(MockKong::raw(404));

    expect(fn () => $kong->client->consumers()->consumerGroups('nobody')->removeAll())
        ->toThrow(NotFoundException::class, 'DELETE /consumers/nobody/consumer_groups failed with HTTP 404.');
});
