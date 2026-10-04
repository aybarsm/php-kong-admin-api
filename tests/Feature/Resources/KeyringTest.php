<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\Keyring as KeyringModel;
use Aybarsm\Kong\AdminApi\Models\KeyringImportInput;
use Aybarsm\Kong\AdminApi\Models\KeyringImportResult;
use Aybarsm\Kong\AdminApi\Models\KeyringInput;
use Aybarsm\Kong\AdminApi\Models\KeyringStatus;
use Aybarsm\Kong\AdminApi\Models\KeyringVaultSyncInput;
use Aybarsm\Kong\AdminApi\Resources\Keyring;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\MockKong;

covers(Keyring::class, Transport::class, KeyringInput::class, KeyringImportInput::class, KeyringVaultSyncInput::class);

it('sends every JSON operation to the spec path and body, never workspace-prefixed', function (string $operation, string $method, string $path, ?string $body): void {
    $kong = MockKong::queue(match ($operation) {
        'status' => MockKong::json(200, Fixture::get('keyring_status')),
        'export', 'generate' => MockKong::json(200, Fixture::get('keyring')),
        'import' => MockKong::json(200, Fixture::get('keyring_import_result')),
        default => MockKong::raw(204),
    });
    $keyring = $kong->client->inWorkspace('team-a')->keyring();

    match ($operation) {
        'status' => $keyring->status(),
        'activate' => $keyring->activate(new KeyringInput(id: 'k1')),
        'export' => $keyring->export(),
        'generate' => $keyring->generate([]),
        'import' => $keyring->import(new KeyringImportInput(id: 'k1', key: 'material')),
        'remove' => $keyring->remove(new KeyringInput(id: 'k1')),
        'syncVault' => $keyring->syncVault(new KeyringVaultSyncInput(token: 't')),
        default => throw new LogicException('Unknown operation ' . $operation),
    };

    expect($kong->lastRequest()->getMethod())->toBe($method)
        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)
        ->and((string) $kong->lastRequest()->getBody())->toBe($body ?? '');
})->with([
    'status' => ['status', 'GET', '/keyring', null],
    'activate' => ['activate', 'POST', '/keyring/activate', '{"id":"k1"}'],
    'export' => ['export', 'POST', '/keyring/export', null],
    'generate' => ['generate', 'POST', '/keyring/generate', '{}'],
    'import' => ['import', 'POST', '/keyring/import', '{"id":"k1","key":"material"}'],
    'remove' => ['remove', 'POST', '/keyring/remove', '{"id":"k1"}'],
    'syncVault' => ['syncVault', 'POST', '/keyring/vault/sync', '{"token":"t"}'],
]);

it('maps the keyring responses', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, Fixture::get('keyring_status')),
        MockKong::json(200, Fixture::get('keyring')),
        MockKong::json(200, Fixture::get('keyring_import_result')),
    );
    $keyring = $kong->client->keyring();

    expect($keyring->status())->toEqual(KeyringStatus::fromArray(Fixture::get('keyring_status')))
        ->and($keyring->export())->toEqual(KeyringModel::fromArray(Fixture::get('keyring')))
        ->and($keyring->import([]))->toEqual(KeyringImportResult::fromArray(Fixture::get('keyring_import_result')));
});

it('sends recover as multipart/form-data with the recovery_private_key field', function (): void {
    $kong = MockKong::queue(MockKong::json(200, Fixture::get('keyring')));

    $result = $kong->client->keyring()->recover("-----BEGIN PRIVATE KEY-----\nabc\n-----END PRIVATE KEY-----");
    $request = $kong->lastRequest();
    $body = (string) $request->getBody();

    expect($request->getMethod())->toBe('POST')
        ->and($request->getUri()->getPath())->toBe('/keyring/recover')
        ->and($request->getHeaderLine('Content-Type'))->toStartWith('multipart/form-data; boundary=')
        ->and($body)->toContain('Content-Disposition: form-data; name="recovery_private_key"; filename="recovery_private_key"')
        ->and($body)->toContain("-----BEGIN PRIVATE KEY-----\nabc\n-----END PRIVATE KEY-----")
        ->and($result)->toEqual(KeyringModel::fromArray(Fixture::get('keyring')));
});

it('rejects a JSON array from recover', function (): void {
    $kong = MockKong::queue(MockKong::json(200, [['id' => 'x']]));

    expect(fn (): KeyringModel => $kong->client->keyring()->recover('pem'))
        ->toThrow(UnexpectedResponseException::class, 'POST /keyring/recover returned a JSON array where an object was expected.');
});
