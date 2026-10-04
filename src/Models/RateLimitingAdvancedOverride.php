<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The rate-limiting-advanced override of a Consumer Group as returned by
 * `PUT /consumer_groups/{ConsumerGroupId}/overrides/plugins/rate-limiting-advanced`.
 */
#[Schema('#/paths/~1consumer_groups~1{ConsumerGroupId}~1overrides~1plugins~1rate-limiting-advanced/put/responses/201/content/application~1json/schema')]
final readonly class RateLimitingAdvancedOverride implements Model
{
    /**
     * @param RateLimitingAdvancedOverrideConfig|null $config
     * @param string|null                             $group  The consumer group
     * @param string|null                             $plugin The name of the plugin
     */
    public function __construct(
        public ?RateLimitingAdvancedOverrideConfig $config = null,
        public ?string $group = null,
        public ?string $plugin = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $config = Data::mapOrNull($data, 'config');

        return new self(
            config: $config === null ? null : RateLimitingAdvancedOverrideConfig::fromArray($config),
            group: Data::stringOrNull($data, 'group'),
            plugin: Data::stringOrNull($data, 'plugin'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'config' => $this->config?->toArray(),
            'group' => $this->group,
            'plugin' => $this->plugin,
        ]);
    }
}
