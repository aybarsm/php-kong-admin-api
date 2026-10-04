<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Request body for `POST /keyring/{activate,generate,remove}` (spec request body `KeyringRequest`).
 */
#[Schema('#/components/requestBodies/KeyringRequest/content/application~1json/schema')]
final readonly class KeyringInput implements Input
{
    /**
     * @param string|null $id  Unique key identifier.
     * @param string|null $key Key material.
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
