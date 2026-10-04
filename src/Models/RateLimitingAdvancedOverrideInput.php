<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Enums\RateLimitWindowType;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Request body for `PUT /consumer_groups/{ConsumerGroupId}/overrides/plugins/rate-limiting-advanced`
 * (spec request body `consumerGroupsConfigResponse`).
 *
 * The spec names these properties with literal dots (`config.limit`, …) and types them as strings,
 * while the 201 response nests them under `config` as integers. They are sent exactly as the spec
 * names them; pass an array to send another shape (spec-notes Q10).
 */
#[Schema('#/components/requestBodies/consumerGroupsConfigResponse/content/application~1json/schema')]
final readonly class RateLimitingAdvancedOverrideInput implements Input
{
    /**
     * @param string|null              $configLimit               An array of one or more requests-per-window limits to apply. Required by the spec on create.
     * @param string|null              $configWindowSize          An array of one or more window sizes to apply a limit to (defined in seconds). Required by the spec on create.
     * @param string|null              $configRetryAfterJitterMax The upper bound of a jitter (random delay) in seconds to be added to the Retry-After header of denied request…
     * @param RateLimitWindowType|null $configWindowType          Set the time window type to either sliding (default) or fixed.
     */
    public function __construct(
        public ?string $configLimit = null,
        public ?string $configWindowSize = null,
        public ?string $configRetryAfterJitterMax = null,
        public ?RateLimitWindowType $configWindowType = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'config.limit' => $this->configLimit,
            'config.window_size' => $this->configWindowSize,
            'config.retry_after_jitter_max' => $this->configRetryAfterJitterMax,
            'config.window_type' => $this->configWindowType?->value,
        ]);
    }
}
