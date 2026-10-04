<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Request body for `POST /keyring/vault/sync` (spec request body `UpdateKeyringVaultSyncRequest`).
 */
#[Schema('#/components/requestBodies/UpdateKeyringVaultSyncRequest/content/application~1json/schema')]
final readonly class KeyringVaultSyncInput implements Input
{
    /**
     * @param string|null $token Optional Vault authentication token.
     */
    public function __construct(
        public ?string $token = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'token' => $this->token,
        ]);
    }
}
