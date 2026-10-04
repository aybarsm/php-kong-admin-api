<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources\Nested;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\PartialLink;
use Aybarsm\Kong\AdminApi\Pagination\CountedPage;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;
use Generator;

/**
 * Plugins linked to one Partial (spec tag "Partial Links"): `/partials/{PartialId}/links`.
 *
 * Obtain it with `$client->partials()->links($partialId)`.
 */
final readonly class PartialLinks extends AbstractResource
{
    private const string SEGMENT = 'links';

    /**
     * List one page of linked plugins with the total `count` (operationId `list-partial-link`).
     *
     * @return CountedPage<PartialLink>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/partials/{PartialId}/links', 'list-partial-link', OperationScope::Both)]
    public function list(?ListOptions $options = null): CountedPage
    {
        return CountedPage::fromArray(
            $this->object(Transport::METHOD_GET, $this->path(OperationScope::Both, self::SEGMENT), $options?->toQuery() ?? []),
            PartialLink::fromArray(...),
        );
    }

    /**
     * Lazily iterate every linked plugin across all pages (operationId `list-partial-link`).
     *
     * @return Generator<int, PartialLink>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/partials/{PartialId}/links', 'list-partial-link', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), PartialLink::fromArray(...), $options);
    }
}
