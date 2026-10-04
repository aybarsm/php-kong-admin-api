<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config` object of RateLimitingAdvancedOverride (spec `#/paths/~1consumer_groups~1{ConsumerGroupId}~1overrides~1plugins~1rate-limiting-advanced/put/responses/201/content/application~1json/schema.config`).
 */
#[Schema('#/paths/~1consumer_groups~1{ConsumerGroupId}~1overrides~1plugins~1rate-limiting-advanced/put/responses/201/content/application~1json/schema/properties/config')]
final readonly class RateLimitingAdvancedOverrideConfig implements Model
{
    /**
     * @param list<int>|null $limit               An array of one or more requests-per-window limits to apply.
     * @param int|null       $retryAfterJitterMax The upper bound of a jitter (random delay) in seconds to be added to the Retry-After header of denied request…
     * @param list<int>|null $windowSize          An array of one or more window sizes to apply a limit to (defined in seconds).
     * @param string|null    $windowType          Set the time window type to either sliding (default) or fixed.
     */
    public function __construct(
        public ?array $limit = null,
        public ?int $retryAfterJitterMax = null,
        public ?array $windowSize = null,
        public ?string $windowType = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            limit: Data::intListOrNull($data, 'limit'),
            retryAfterJitterMax: Data::intOrNull($data, 'retry_after_jitter_max'),
            windowSize: Data::intListOrNull($data, 'window_size'),
            windowType: Data::stringOrNull($data, 'window_type'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'limit' => $this->limit,
            'retry_after_jitter_max' => $this->retryAfterJitterMax,
            'window_size' => $this->windowSize,
            'window_type' => $this->windowType,
        ]);
    }
}
