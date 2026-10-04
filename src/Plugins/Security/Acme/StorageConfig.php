<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Security\Acme;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.storage_config` object of the ACME plugin (doc `Security/acme.md`).
 */
#[PluginSchema('Security/acme.md', '#/properties/config/properties/storage_config')]
final readonly class StorageConfig implements Model
{
    /**
     * @param StorageConfigConsul|null     $consul
     * @param array<array-key, mixed>|null $kong
     * @param StorageConfigRedis|null      $redis
     * @param StorageConfigShm|null        $shm
     * @param StorageConfigVault|null      $vault
     */
    public function __construct(
        public ?StorageConfigConsul $consul = null,
        public ?array $kong = null,
        public ?StorageConfigRedis $redis = null,
        public ?StorageConfigShm $shm = null,
        public ?StorageConfigVault $vault = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $consul = Data::mapOrNull($data, 'consul');
        $redis = Data::mapOrNull($data, 'redis');
        $shm = Data::mapOrNull($data, 'shm');
        $vault = Data::mapOrNull($data, 'vault');

        return new self(
            consul: $consul === null ? null : StorageConfigConsul::fromArray($consul),
            kong: Data::freeFormOrNull($data, 'kong'),
            redis: $redis === null ? null : StorageConfigRedis::fromArray($redis),
            shm: $shm === null ? null : StorageConfigShm::fromArray($shm),
            vault: $vault === null ? null : StorageConfigVault::fromArray($vault),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'consul' => $this->consul?->toArray(),
            'kong' => $this->kong,
            'redis' => $this->redis?->toArray(),
            'shm' => $this->shm?->toArray(),
            'vault' => $this->vault?->toArray(),
        ]);
    }
}
