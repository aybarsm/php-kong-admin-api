<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Security\Acme;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.storage_config.redis.extra_options` object of the ACME plugin (doc `Security/acme.md`).
 */
#[PluginSchema('Security/acme.md', '#/properties/config/properties/storage_config/properties/redis/properties/extra_options')]
final readonly class AcmeStorageConfigRedisExtraOptions implements Model
{
    /**
     * @param string|null $namespace A namespace to prepend to all keys stored in Redis. Default: ``.
     * @param float|null  $scanCount The number of keys to return in Redis SCAN calls. Default: `10`.
     */
    public function __construct(
        public ?string $namespace = null,
        public ?float $scanCount = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            namespace: Data::stringOrNull($data, 'namespace'),
            scanCount: Data::floatOrNull($data, 'scan_count'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'namespace' => $this->namespace,
            'scan_count' => $this->scanCount,
        ]);
    }
}
