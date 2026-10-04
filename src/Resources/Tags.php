<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\TagEntry;
use Aybarsm\Kong\AdminApi\Pagination\Page;

/**
 * Tags (spec tag "Tags"): `/tags` and `/tags/{tag}`.
 *
 * The spec describes both lists as paginated but declares no `size`, `offset` or `tags` query
 * parameters and returns only `next`, so these methods take no options and there is no `all()`
 * walker (spec-notes Q7).
 */
final readonly class Tags extends AbstractResource
{
    private const string SEGMENT = 'tags';

    /**
     * List entity/tag pairs across all entity types (operationId `get-tags`).
     *
     * @return Page<TagEntry>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/tags', 'get-tags', OperationScope::GlobalOnly)]
    public function list(): Page
    {
        return $this->page($this->path(OperationScope::GlobalOnly, self::SEGMENT), TagEntry::fromArray(...));
    }

    /**
     * List the entities carrying one tag (operationId `get-tags-tag`).
     *
     * @return Page<TagEntry>
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $tag is empty
     */
    #[Operation(Transport::METHOD_GET, '/tags/{tag}', 'get-tags-tag', OperationScope::GlobalOnly)]
    public function byTag(string $tag): Page
    {
        return $this->page($this->path(OperationScope::GlobalOnly, self::SEGMENT, $tag), TagEntry::fromArray(...));
    }
}
