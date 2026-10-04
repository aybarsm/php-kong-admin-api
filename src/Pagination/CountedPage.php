<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Pagination;

use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;

/**
 * A page whose envelope also reports a total: the spec's `{count, data, next, offset}`
 * (e.g. `GET /partials/{PartialId}/links`).
 *
 * @template-covariant T
 */
final readonly class CountedPage
{
    /**
     * @param list<T>     $data   items on this page
     * @param int|null    $count  total number of items across all pages, as reported by Kong
     * @param string|null $offset pass to the next list call to fetch the following page
     * @param string|null $next   URI of the next page as returned by Kong; not followed by this client
     */
    public function __construct(
        public array $data,
        public ?int $count = null,
        public ?string $offset = null,
        public ?string $next = null,
    ) {
    }

    /**
     * @template U
     *
     * @param array<string, mixed>              $payload
     * @param callable(array<string, mixed>): U $map
     *
     * @return self<U>
     *
     * @throws UnexpectedResponseException
     */
    public static function fromArray(array $payload, callable $map): self
    {
        return new self(
            array_map($map, Data::listOfMaps($payload, 'data')),
            Data::intOrNull($payload, 'count'),
            Data::stringOrNull($payload, 'offset'),
            Data::stringOrNull($payload, 'next'),
        );
    }

    /**
     * Whether Kong returned an offset for a following page.
     */
    public function hasMore(): bool
    {
        return $this->offset !== null;
    }
}
