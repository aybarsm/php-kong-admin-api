<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Pagination;

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;

/**
 * Query options for paginated list operations (spec parameters `PaginationSize`,
 * `PaginationOffset` and `PaginationTagsFilter`).
 */
final readonly class ListOptions
{
    /** `PaginationSize.minimum` */
    public const int MIN_SIZE = 1;

    /** `PaginationSize.maximum` */
    public const int MAX_SIZE = 1000;

    /**
     * @param int|null       $size   page size; Kong's default (100) applies when null
     * @param string|null    $offset the `offset` returned by a previous page
     * @param TagFilter|null $tags   tag filter
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        public ?int $size = null,
        public ?string $offset = null,
        public ?TagFilter $tags = null,
    ) {
        if ($size !== null && ($size < self::MIN_SIZE || $size > self::MAX_SIZE)) {
            throw new InvalidArgumentException(sprintf('size must be between %d and %d.', self::MIN_SIZE, self::MAX_SIZE));
        }
        if ($offset === '') {
            throw new InvalidArgumentException('offset must not be empty; pass null for the first page.');
        }
    }

    /**
     * A copy positioned at another page.
     *
     * @throws InvalidArgumentException
     */
    public function withOffset(?string $offset): self
    {
        return new self($this->size, $offset, $this->tags);
    }

    /**
     * @return array<string, string|int|null>
     */
    public function toQuery(): array
    {
        return [
            'size' => $this->size,
            'offset' => $this->offset,
            'tags' => $this->tags?->toQueryValue(),
        ];
    }
}
