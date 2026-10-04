<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\License;
use Aybarsm\Kong\AdminApi\Models\LicenseInput;
use Aybarsm\Kong\AdminApi\Models\LicenseReport;

/**
 * Licenses (spec tag "Licenses"): `/licenses`, `/licenses/{licenseId}` and `/license/report`.
 * All paths are global (no `/{workspace}` twin).
 */
final readonly class Licenses extends AbstractResource
{
    private const string SEGMENT = 'licenses';

    /**
     * List Licenses (operationId `get-licenses`). The spec's response is a single `LicenseResponse`, not a
     * list envelope; implemented literally (spec-notes Q8).
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/licenses', 'get-licenses', OperationScope::GlobalOnly)]
    public function list(): License
    {
        return License::fromArray($this->object(Transport::METHOD_GET, $this->path(OperationScope::GlobalOnly, self::SEGMENT)));
    }

    /**
     * Add a License (operationId `create-licenses`, body `LicenseRequest`).
     *
     * @param LicenseInput|array<string, mixed> $license
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/licenses', 'create-licenses', OperationScope::GlobalOnly)]
    public function create(LicenseInput|array $license): License
    {
        return License::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT),
            body: $license,
        ));
    }

    /**
     * Get a License by ID (operationId `get-licenses-license-id`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/licenses/{licenseId}', 'get-licenses-license-id', OperationScope::GlobalOnly)]
    public function get(string $id): License
    {
        return License::fromArray($this->object(Transport::METHOD_GET, $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id)));
    }

    /**
     * Update a License (operationId `update-a-license`, PATCH, body `LicenseRequest`).
     *
     * @param LicenseInput|array<string, mixed> $license
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/licenses/{licenseId}', 'update-a-license', OperationScope::GlobalOnly)]
    public function update(string $id, LicenseInput|array $license): License
    {
        return License::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id),
            body: $license,
        ));
    }

    /**
     * Create or replace a License by ID (operationId `update-licenses-license-id`, PUT, body `LicenseRequest`).
     *
     * @param LicenseInput|array<string, mixed> $license
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/licenses/{licenseId}', 'update-licenses-license-id', OperationScope::GlobalOnly)]
    public function upsert(string $id, LicenseInput|array $license): License
    {
        return License::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id),
            body: $license,
        ));
    }

    /**
     * Delete a License (operationId `delete-licenses-license-id`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/licenses/{licenseId}', 'delete-licenses-license-id', OperationScope::GlobalOnly)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::GlobalOnly, self::SEGMENT, $id));
    }

    /**
     * The license usage report (operationId `get-license-report`, path `/license/report`).
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/license/report', 'get-license-report', OperationScope::GlobalOnly)]
    public function report(): LicenseReport
    {
        return LicenseReport::fromArray($this->object(Transport::METHOD_GET, $this->path(OperationScope::GlobalOnly, 'license', 'report')));
    }
}
