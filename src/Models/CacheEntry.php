<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * A cache entry from `GET /cache/{key}` (spec response `CacheEntryFoundResponse`).
 */
#[Schema('#/components/responses/CacheEntryFoundResponse/content/application~1json/schema')]
final readonly class CacheEntry implements Model
{
    /**
     * @param string|null $message Cached value or a message.
     * @param int|null    $ttl     Time-to-live (TTL) of the cached entry.
     */
    public function __construct(
        public ?string $message = null,
        public ?int $ttl = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            message: Data::stringOrNull($data, 'message'),
            ttl: Data::intOrNull($data, 'ttl'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'message' => $this->message,
            'ttl' => $this->ttl,
        ]);
    }
}
