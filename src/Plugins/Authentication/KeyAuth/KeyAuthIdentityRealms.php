<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\KeyAuth;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.identity_realms[]` object of the Key Auth plugin (doc `Authentication/key-auth.md`).
 */
#[PluginSchema('Authentication/key-auth.md', '#/properties/config/properties/identity_realms/items')]
final readonly class KeyAuthIdentityRealms implements Model
{
    /**
     * @param string|null                     $id     A string representing a UUID (universally unique identifier).
     * @param string|null                     $region
     * @param KeyAuthIdentityRealmsScope|null $scope  Default: `cp`.
     */
    public function __construct(
        public ?string $id = null,
        public ?string $region = null,
        public ?KeyAuthIdentityRealmsScope $scope = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            id: Data::stringOrNull($data, 'id'),
            region: Data::stringOrNull($data, 'region'),
            scope: Data::enumOrNull($data, 'scope', KeyAuthIdentityRealmsScope::class),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'id' => $this->id,
            'region' => $this->region,
            'scope' => $this->scope?->value,
        ]);
    }
}
