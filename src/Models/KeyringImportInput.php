<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Request body for `POST /keyring/import` (spec request body `CreateKeyringImportRequest`).
 */
#[Schema('#/components/requestBodies/CreateKeyringImportRequest/content/application~1json/schema')]
final readonly class KeyringImportInput implements Input
{
    /**
     * @param string|null $id
     * @param string|null $key
     */
    public function __construct(
        public ?string $id = null,
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
}
