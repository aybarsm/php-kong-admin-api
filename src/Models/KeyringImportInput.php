<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;
use SensitiveParameter;

/**
 * Request body for `POST /keyring/import` (spec request body `CreateKeyringImportRequest`).
 */
#[Schema('#/components/requestBodies/CreateKeyringImportRequest/content/application~1json/schema')]
final readonly class KeyringImportInput implements Input
{
    /** Properties the spec marks `x-encrypted` (plus reviewed secrets, spec-notes Q18); redacted in __debugInfo(). */
    private const array ENCRYPTED = ['key'];

    /**
     * @param string|null $id
     * @param string|null $key
     */
    public function __construct(
        public ?string $id = null,
        #[SensitiveParameter]
        public ?string $key = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'id' => $this->id,
            'key' => $this->key,
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
