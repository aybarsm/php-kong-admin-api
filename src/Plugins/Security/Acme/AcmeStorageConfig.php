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
final readonly class AcmeStorageConfig implements Model
{
    /**
     * @param AcmeStorageConfigConsul|null $consul
     * @param array<array-key, mixed>|null $kong
     * @param AcmeStorageConfigRedis|null  $redis
     * @param AcmeStorageConfigShm|null    $shm
     * @param AcmeStorageConfigVault|null  $vault
     */
    public function __construct(
        public ?AcmeStorageConfigConsul $consul = null,
        public ?array $kong = null,
        public ?AcmeStorageConfigRedis $redis = null,
        public ?AcmeStorageConfigShm $shm = null,
        public ?AcmeStorageConfigVault $vault = null,
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
            consul: $consul === null ? null : AcmeStorageConfigConsul::fromArray($consul),
            kong: Data::freeFormOrNull($data, 'kong'),
            redis: $redis === null ? null : AcmeStorageConfigRedis::fromArray($redis),
            shm: $shm === null ? null : AcmeStorageConfigShm::fromArray($shm),
            vault: $vault === null ? null : AcmeStorageConfigVault::fromArray($vault),
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
