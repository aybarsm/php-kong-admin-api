<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Models\TagEntry;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\Tags;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(Tags::class, TagEntry::class);

it('lists all tags without query parameters (spec-notes Q7)', function (): void {
    $kong = MockKong::queue(MockKong::json(200, ['data' => [Fixture::get('tag_entry')], 'next' => '/tags?offset=abc']));

    $page = $kong->client->tags()->list();

    expect($kong->lastRequest()->getMethod())->toBe('GET')
        ->and((string) $kong->lastRequest()->getUri())->toBe('http://kong.test/tags')
        ->and($page->data)->toEqual([TagEntry::fromArray(Fixture::get('tag_entry'))])
        ->and($page->next)->toBe('/tags?offset=abc')
        ->and($page->offset)->toBeNull()
        ->and($page->hasMore())->toBeFalse();
});

it('lists the entities carrying one tag', function (): void {
    $kong = MockKong::queue(MockKong::json(200, ['data' => [Fixture::get('tag_entry')], 'next' => null]));

    $page = $kong->client->tags()->byTag('team a');

    expect($kong->lastRequest()->getUri()->getPath())->toBe('/tags/team%20a')
        ->and($page->data[0]->tag)->toBe(Fixture::get('tag_entry')['tag']);
});

it('never prefixes a workspace: the tag endpoints are global only', function (): void {
    $kong = MockKong::queue(MockKong::json(200, ['data' => []]), MockKong::json(200, ['data' => []]));

    $kong->client->inWorkspace('team-a')->tags()->list();
    $kong->client->inWorkspace('team-a')->tags()->byTag('t');

    expect($kong->requestAt(0)->getUri()->getPath())->toBe('/tags')
        ->and($kong->requestAt(1)->getUri()->getPath())->toBe('/tags/t');
});

it('rejects an empty tag', function (): void {
    expect(fn (): Page => MockKong::queue()->client->tags()->byTag(''))->toThrow(InvalidArgumentException::class);
});
