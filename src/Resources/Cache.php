<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Exceptions\NotFoundException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\CacheEntry;

/**
 * The node cache (spec tag "Cache"): `/cache` and `/cache/{key}`. Global paths.
 */
final readonly class Cache extends AbstractResource
{
    private const string SEGMENT = 'cache';

    /**
     * Get a cache entry (operationId `get-cache-by-key`).
     *
     * @throws NotFoundException when the key is not cached
     * @throws KongApiException
     * @throws InvalidArgumentException when $key is empty
     */
    #[Operation(Transport::METHOD_GET, '/cache/{key}', 'get-cache-by-key', OperationScope::GlobalOnly)]
    public function get(string $key): CacheEntry
    {
        return CacheEntry::fromArray($this->object(Transport::METHOD_GET, $this->path(OperationScope::GlobalOnly, self::SEGMENT, $key)));
    }

    /**
     * Delete one cache entry (operationId `deleteCacheByKey`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $key is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/cache/{key}', 'deleteCacheByKey', OperationScope::GlobalOnly)]
    public function delete(string $key): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::GlobalOnly, self::SEGMENT, $key));
    }

    /**
     * Delete every cache entry (operationId `delete-cache-entries`).
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_DELETE, '/cache', 'delete-cache-entries', OperationScope::GlobalOnly)]
    public function flush(): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::GlobalOnly, self::SEGMENT));
    }
}
