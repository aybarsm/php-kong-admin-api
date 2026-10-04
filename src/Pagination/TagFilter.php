<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Pagination;

use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;

/**
 * The `tags` list filter (spec parameter `PaginationTagsFilter`): tags joined with `,` mean AND,
 * joined with `/` mean OR. The spec does not define mixing the two, so a filter uses one separator.
 */
final readonly class TagFilter
{
    public const string AND_SEPARATOR = ',';
    public const string OR_SEPARATOR = '/';

    /**
     * @param non-empty-list<string> $tags
     */
    private function __construct(
        public array $tags,
        public string $separator,
    ) {
    }

    /**
     * Matches entities carrying every given tag.
     *
     * @throws InvalidArgumentException
     */
    public static function allOf(string $tag, string ...$more): self
    {
        return new self(self::validated([$tag, ...array_values($more)]), self::AND_SEPARATOR);
    }

    /**
     * Matches entities carrying at least one of the given tags.
     *
     * @throws InvalidArgumentException
     */
    public static function anyOf(string $tag, string ...$more): self
    {
        return new self(self::validated([$tag, ...array_values($more)]), self::OR_SEPARATOR);
    }

    /**
     * The value sent as the `tags` query parameter.
     */
    public function toQueryValue(): string
    {
        return implode($this->separator, $this->tags);
    }

    /**
     * @param non-empty-list<string> $tags
     *
     * @return non-empty-list<string>
     *
     * @throws InvalidArgumentException
     */
    private static function validated(array $tags): array
    {
        foreach ($tags as $tag) {
            if ($tag === '' || str_contains($tag, self::AND_SEPARATOR) || str_contains($tag, self::OR_SEPARATOR)) {
                throw new InvalidArgumentException(sprintf(
                    'Tags must be non-empty and must not contain "%s" or "%s".',
                    self::AND_SEPARATOR,
                    self::OR_SEPARATOR,
                ));
            }
        }

        return $tags;
    }
}
