<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\Models\Consumer;
use Aybarsm\Kong\AdminApi\Models\ConsumerGroupMemberInput;
use Aybarsm\Kong\AdminApi\Models\ConsumerGroupMembership;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Resources\ConsumerGroups;
use Aybarsm\Kong\AdminApi\Resources\Nested\ConsumerGroupConsumers;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(ConsumerGroupConsumers::class, ConsumerGroups::class, ConsumerGroupMemberInput::class);

it('sends every operation to the spec path', function (string $operation, string $method, string $path, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'list' => MockKong::json(200, ['data' => [Fixture::get('consumer')]]),
        'add' => MockKong::json(201, Fixture::get('consumer_group_membership')),
        default => MockKong::raw(204),
    });
    $members = $kong->client->consumerGroups()->consumers('gold tier');

    match ($operation) {
        'list' => $members->list(),
        'add' => $members->add(new ConsumerGroupMemberInput(consumer: 'alice')),
        'removeAll' => $members->removeAll(),
        'remove' => $members->remove('alice'),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and((string) $kong->lastRequest()->getBody())->toBe($body ?? '');
})->with([
    'list' => ['list', 'GET', '/consumer_groups/gold%20tier/consumers', null],
    'add' => ['add', 'POST', '/consumer_groups/gold%20tier/consumers', '{"consumer":"alice"}'],
    'removeAll' => ['removeAll', 'DELETE', '/consumer_groups/gold%20tier/consumers', null],
    'remove' => ['remove', 'DELETE', '/consumer_groups/gold%20tier/consumers/alice', null],
]);

it('lists and walks member consumers', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('consumer')], 'offset' => 'p2']),
        MockKong::json(200, ['data' => [Fixture::get('consumer')]]),
    );

    $all = iterator_to_array($kong->client->consumerGroups()->consumers('g')->all(new ListOptions(size: 1)));

    expect($all)->toEqual([Consumer::fromArray(Fixture::get('consumer')), Consumer::fromArray(Fixture::get('consumer'))])
        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p2']);
});

it('maps the membership returned when adding a consumer', function (): void {
    $kong = MockKong::queue(MockKong::json(201, Fixture::get('consumer_group_membership')));

    $membership = $kong->client->consumerGroups()->consumers('g')->add(['consumer' => 'alice']);

    expect($membership)->toEqual(ConsumerGroupMembership::fromArray(Fixture::get('consumer_group_membership')))
        ->and($membership->consumers)->toHaveCount(1);
});

it('prefixes the workspace', function (): void {
    $kong = MockKong::queue(MockKong::raw(204));

    $kong->client->inWorkspace('team-a')->consumerGroups()->consumers('g')->remove('alice');

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/team-a/consumer_groups/g/consumers/alice');
});

it('rejects empty IDs before sending anything', function (): void {
    $kong = MockKong::queue();

    expect(fn (): ConsumerGroupConsumers => $kong->client->consumerGroups()->consumers(''))
        ->toThrow(InvalidArgumentException::class, 'Consumer Group ID or name must not be empty.')
        ->and(fn () => $kong->client->consumerGroups()->consumers('g')->remove(''))->toThrow(InvalidArgumentException::class)
        ->and($kong->requestCount())->toBe(0);
});

it('maps 404 when the group or membership does not exist', function (): void {
    $kong = MockKong::queue(MockKong::raw(404));

    expect(fn () => $kong->client->consumerGroups()->consumers('g')->removeAll())
        ->toThrow(NotFoundException::class, 'DELETE /consumer_groups/g/consumers failed with HTTP 404.');
});
