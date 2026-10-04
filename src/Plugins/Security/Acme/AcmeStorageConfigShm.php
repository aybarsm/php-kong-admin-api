<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Security\Acme;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.storage_config.shm` object of the ACME plugin (doc `Security/acme.md`).
 */
#[PluginSchema('Security/acme.md', '#/properties/config/properties/storage_config/properties/shm')]
final readonly class AcmeStorageConfigShm implements Model
{
    /**
     * @param string|null $shmName Name of shared memory zone used for Kong API gateway storage Default: `kong`.
     */
    public function __construct(
        public ?string $shmName = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            shmName: Data::stringOrNull($data, 'shm_name'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'shm_name' => $this->shmName,
        ]);
    }
}
