<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Authentication\KeyAuth;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.principals` object of the Key Auth plugin (doc `Authentication/key-auth.md`).
 */
#[PluginSchema('Authentication/key-auth.md', '#/properties/config/properties/principals')]
final readonly class Principals implements Model
{
    /**
     * @param string|null $directory   The Kong Identity directory instance to authenticate against. Default: `default`.
     * @param bool|null   $enabled     When true, authenticate against Kong Identity instead of local credentials. Default: `false`.
     * @param bool|null   $errorOnMiss When true (default), return 401 if no matching principal is found in Kong Identity. Default: `true`.
     */
    public function __construct(
        public ?string $directory = null,
        public ?bool $enabled = null,
        public ?bool $errorOnMiss = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            directory: Data::stringOrNull($data, 'directory'),
            enabled: Data::boolOrNull($data, 'enabled'),
            errorOnMiss: Data::boolOrNull($data, 'error_on_miss'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'directory' => $this->directory,
            'enabled' => $this->enabled,
            'error_on_miss' => $this->errorOnMiss,
        ]);
    }
}
