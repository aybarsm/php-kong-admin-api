<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\KeyringImportInput;
use Aybarsm\Kong\AdminApi\Models\KeyringImportResult;
use Aybarsm\Kong\AdminApi\Models\KeyringInput;
use Aybarsm\Kong\AdminApi\Models\KeyringStatus;
use Aybarsm\Kong\AdminApi\Models\KeyringVaultSyncInput;
use SensitiveParameter;

/**
 * The keyring (spec tag "Keyring"): `/keyring` and `/keyring/{activate,export,generate,import,recover,remove}`,
 * `/keyring/vault/sync`. Global paths. All operations except `status()` are POSTs.
 */
final readonly class Keyring extends AbstractResource
{
    private const string SEGMENT = 'keyring';

    /**
     * The active key and all key IDs (operationId `get-keyring`).
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/keyring', 'get-keyring', OperationScope::GlobalOnly)]
    public function status(): KeyringStatus
    {
        return KeyringStatus::fromArray($this->object(Transport::METHOD_GET, $this->path(OperationScope::GlobalOnly, self::SEGMENT)));
    }

    /**
     * Activate a key (operationId `create-keyring-activate`, body `KeyringRequest`).
     *
     * @param KeyringInput|array<string, mixed> $key
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/keyring/activate', 'create-keyring-activate', OperationScope::GlobalOnly)]
    public function activate(KeyringInput|array $key): void
    {
        $this->none(Transport::METHOD_POST, $this->path(OperationScope::GlobalOnly, self::SEGMENT, 'activate'), body: $key);
    }

    /**
     * Export the keyring (operationId `update-keyring-export`, POST without a body).
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/keyring/export', 'update-keyring-export', OperationScope::GlobalOnly)]
    public function export(): \Aybarsm\Kong\AdminApi\Models\Keyring
    {
        return \Aybarsm\Kong\AdminApi\Models\Keyring::fromArray(
            $this->object(Transport::METHOD_POST, $this->path(OperationScope::GlobalOnly, self::SEGMENT, 'export')),
        );
    }

    /**
     * Generate a key (operationId `create-keyring-generate`, body `KeyringRequest`).
     *
     * @param KeyringInput|array<string, mixed> $key
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/keyring/generate', 'create-keyring-generate', OperationScope::GlobalOnly)]
    public function generate(KeyringInput|array $key): \Aybarsm\Kong\AdminApi\Models\Keyring
    {
        return \Aybarsm\Kong\AdminApi\Models\Keyring::fromArray(
            $this->object(Transport::METHOD_POST, $this->path(OperationScope::GlobalOnly, self::SEGMENT, 'generate'), body: $key),
        );
    }

    /**
     * Import a key (operationId `create-keyring-import`, body `CreateKeyringImportRequest`).
     *
     * @param KeyringImportInput|array<string, mixed> $key
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/keyring/import', 'create-keyring-import', OperationScope::GlobalOnly)]
    public function import(KeyringImportInput|array $key): KeyringImportResult
    {
        return KeyringImportResult::fromArray(
            $this->object(Transport::METHOD_POST, $this->path(OperationScope::GlobalOnly, self::SEGMENT, 'import'), body: $key),
        );
    }

    /**
     * Recover the keyring with a recovery private key (operationId `create-keyring-recover`). The spec accepts
     * only `multipart/form-data` here (field `recovery_private_key`, binary), so this is sent as multipart.
     *
     * @param string $recoveryPrivateKey the recovery private key in PEM format
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/keyring/recover', 'create-keyring-recover', OperationScope::GlobalOnly)]
    public function recover(#[SensitiveParameter] string $recoveryPrivateKey): \Aybarsm\Kong\AdminApi\Models\Keyring
    {
        $path = $this->path(OperationScope::GlobalOnly, self::SEGMENT, 'recover');
        $payload = $this->transport->multipart(Transport::METHOD_POST, $path, ['recovery_private_key' => $recoveryPrivateKey]);

        return \Aybarsm\Kong\AdminApi\Models\Keyring::fromArray($this->asObject($payload, Transport::METHOD_POST, $path));
    }

    /**
     * Remove a key (operationId `delete-keyring-remove`, POST, body `KeyringRequest`).
     *
     * @param KeyringInput|array<string, mixed> $key
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/keyring/remove', 'delete-keyring-remove', OperationScope::GlobalOnly)]
    public function remove(KeyringInput|array $key): void
    {
        $this->none(Transport::METHOD_POST, $this->path(OperationScope::GlobalOnly, self::SEGMENT, 'remove'), body: $key);
    }

    /**
     * Sync the keyring with Vault (operationId `update-keyring-vault-sync`, body `UpdateKeyringVaultSyncRequest`).
     *
     * @param KeyringVaultSyncInput|array<string, mixed> $sync
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/keyring/vault/sync', 'update-keyring-vault-sync', OperationScope::GlobalOnly)]
    public function syncVault(KeyringVaultSyncInput|array $sync): void
    {
        $this->none(Transport::METHOD_POST, $this->path(OperationScope::GlobalOnly, self::SEGMENT, 'vault', 'sync'), body: $sync);
    }
}
