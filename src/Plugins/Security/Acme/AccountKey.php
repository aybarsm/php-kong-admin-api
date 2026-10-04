<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Security\Acme;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.account_key` object of the ACME plugin (doc `Security/acme.md`).
 */
#[PluginSchema('Security/acme.md', '#/properties/config/properties/account_key')]
final readonly class AccountKey implements Model
{
    /** Properties the plugin doc marks `x-encrypted`; redacted in __debugInfo(). */
    private const array ENCRYPTED = ['keyId', 'keySet'];

    /**
     * @param string      $keyId  The Key ID.
     * @param string|null $keySet The name of the key set to associate the Key ID with.
     */
    public function __construct(
        public string $keyId,
        public ?string $keySet = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            keyId: Data::string($data, 'key_id'),
            keySet: Data::stringOrNull($data, 'key_set'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'key_id' => $this->keyId,
            'key_set' => $this->keySet,
        ]);
    }

    /**
     * Redacts `x-encrypted` values.
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        $values = get_object_vars($this);
        foreach (self::ENCRYPTED as $property) {
            if ($values[$property] !== null) {
                $values[$property] = '***';
            }
        }

        return $values;
    }
}
