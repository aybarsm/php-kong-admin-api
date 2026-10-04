<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Pagination\TagFilter;

covers(ListOptions::class, Page::class, TagFilter::class);

it('accepts the spec size bounds', function (int $size): void {
    expect((new ListOptions(size: $size))->toQuery()['size'])->toBe($size);
})->with([1, 100, 1000]);

it('rejects sizes outside the spec bounds', function (int $size): void {
    expect(fn (): ListOptions => new ListOptions(size: $size))
        ->toThrow(InvalidArgumentException::class, 'size must be between 1 and 1000.');
})->with([0, -1, 1001]);

it('rejects an empty offset', function (): void {
    expect(fn (): ListOptions => new ListOptions(offset: ''))->toThrow(InvalidArgumentException::class);
});

it('builds query parameters and keeps settings when moving to another offset', function (): void {
    $options = new ListOptions(size: 50, tags: TagFilter::anyOf('x'));
    $next = $options->withOffset('abc');

    expect($options->toQuery())->toBe(['size' => 50, 'offset' => null, 'tags' => 'x'])
        ->and($next->toQuery())->toBe(['size' => 50, 'offset' => 'abc', 'tags' => 'x'])
        ->and($next->withOffset(null)->offset)->toBeNull();
});

it('joins tags with , for AND and / for OR', function (): void {
    expect(TagFilter::allOf('a', 'b', 'c')->toQueryValue())->toBe('a,b,c')
        ->and(TagFilter::anyOf('a', 'b')->toQueryValue())->toBe('a/b')
        ->and(TagFilter::allOf('solo')->tags)->toBe(['solo'])
        ->and(TagFilter::anyOf('solo')->separator)->toBe('/');
});

it('rejects tags that are empty or contain a separator', function (string $tag): void {
    expect(fn (): TagFilter => TagFilter::allOf('ok', $tag))->toThrow(InvalidArgumentException::class)
        ->and(fn (): TagFilter => TagFilter::anyOf($tag))->toThrow(InvalidArgumentException::class);
})->with(['', 'a,b', 'a/b']);

it('maps a list envelope', function (): void {
    $page = Page::fromArray(
        ['data' => [['n' => 1], ['n' => 2]], 'offset' => 'o', 'next' => '/x?offset=o'],
        static fn (array $row): string => json_encode($row, JSON_THROW_ON_ERROR),
    );

    expect($page->data)->toBe(['{"n":1}', '{"n":2}'])
        ->and($page->offset)->toBe('o')
        ->and($page->next)->toBe('/x?offset=o')
        ->and($page->hasMore())->toBeTrue();
});

it('treats an empty JSON object as an empty list', function (): void {
    expect(Page::fromArray(['data' => []], static fn (array $row): int => count($row))->data)->toBe([]);
});

it('rejects envelopes without a data list', function (array $payload): void {
    expect(fn (): Page => Page::fromArray($payload, static fn (array $row): int => count($row)))
        ->toThrow(UnexpectedResponseException::class);
})->with([
    'missing' => [['next' => null]],
    'object' => [['data' => ['a' => ['x' => 1]]]],
    'scalar items' => [['data' => [1, 2]]],
    'list items' => [['data' => [[1, 2]]]],
]);
