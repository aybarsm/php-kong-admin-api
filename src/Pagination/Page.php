<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Pagination;

use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;

/**
 * One page of a list operation: the spec's `{data, next, offset}` envelope.
 *
 * @template-covariant T
 */
final readonly class Page
{
    /**
     * @param list<T>     $data   items on this page
     * @param string|null $offset pass to the next list call to fetch the following page (spec `PaginationOffsetResponse`)
     * @param string|null $next   URI of the next page as returned by Kong (spec `PaginationNextResponse`); not followed by this client
     */
    public function __construct(
        public array $data,
        public ?string $offset = null,
        public ?string $next = null,
    ) {
    }

    /**
     * Maps a decoded list envelope.
     *
     * @template U
     *
     * @param array<string, mixed>                $payload
     * @param callable(array<string, mixed>): U   $map
     *
     * @return self<U>
     *
     * @throws UnexpectedResponseException
     */
    public static function fromArray(array $payload, callable $map): self
    {
        return new self(
            array_map($map, Data::listOfMaps($payload, 'data')),
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
