<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\BasicAuth;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.brute_force_protection` object of the Basic Auth plugin (doc `Authentication/basic-auth.md`).
 */
#[PluginSchema('Authentication/basic-auth.md', '#/properties/config/properties/brute_force_protection')]
final readonly class BruteForceProtection implements Model
{
    /**
     * @param BruteForceProtectionRedis|null    $redis    Redis configuration
     * @param BruteForceProtectionStrategy|null $strategy The brute force protection strategy to use for retrieving and incrementing the limits. Default: `off`.
     */
    public function __construct(
        public ?BruteForceProtectionRedis $redis = null,
        public ?BruteForceProtectionStrategy $strategy = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $redis = Data::mapOrNull($data, 'redis');

        return new self(
            redis: $redis === null ? null : BruteForceProtectionRedis::fromArray($redis),
            strategy: Data::enumOrNull($data, 'strategy', BruteForceProtectionStrategy::class),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'redis' => $this->redis?->toArray(),
            'strategy' => $this->strategy?->value,
        ]);
    }
}
