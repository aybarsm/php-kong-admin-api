<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\CaCertificate;
use Aybarsm\Kong\AdminApi\Models\CaCertificateInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Generator;

/**
 * CA Certificates (spec tag "CA Certificates"): `/ca_certificates` and `/ca_certificates/{CACertificateId}`.
 */
final readonly class CaCertificates extends AbstractResource
{
    private const string SEGMENT = 'ca_certificates';

    /**
     * List one page of CA Certificates (operationId `list-ca_certificate`).
     *
     * @return Page<CaCertificate>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/ca_certificates', 'list-ca_certificate', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), CaCertificate::fromArray(...), $options);
    }

    /**
     * Lazily iterate every CA Certificate across all pages (operationId `list-ca_certificate`).
     *
     * @return Generator<int, CaCertificate>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/ca_certificates', 'list-ca_certificate', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), CaCertificate::fromArray(...), $options);
    }

    /**
     * Get a CA Certificate by ID (operationId `get-ca_certificate`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/ca_certificates/{CACertificateId}', 'get-ca_certificate', OperationScope::Both)]
    public function get(string $id): CaCertificate
    {
        return CaCertificate::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
        ));
    }

    /**
     * Create a CA Certificate (operationId `create-ca_certificate`, body `CACertificate`).
     *
     * @param CaCertificateInput|array<string, mixed> $caCertificate
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/ca_certificates', 'create-ca_certificate', OperationScope::Both)]
    public function create(CaCertificateInput|array $caCertificate): CaCertificate
    {
        return CaCertificate::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $caCertificate,
        ));
    }

    /**
     * Update fields of a CA Certificate (operationId `update-ca_certificate`, PATCH, body `CACertificate`).
     *
     * @param CaCertificateInput|array<string, mixed> $caCertificate only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/ca_certificates/{CACertificateId}', 'update-ca_certificate', OperationScope::Both)]
    public function update(string $id, CaCertificateInput|array $caCertificate): CaCertificate
    {
        return CaCertificate::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $caCertificate,
        ));
    }

    /**
     * Create or replace a CA Certificate by ID (operationId `upsert-ca_certificate`, PUT, body `CACertificate`).
     *
     * @param CaCertificateInput|array<string, mixed> $caCertificate
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/ca_certificates/{CACertificateId}', 'upsert-ca_certificate', OperationScope::Both)]
    public function upsert(string $id, CaCertificateInput|array $caCertificate): CaCertificate
    {
        return CaCertificate::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $caCertificate,
        ));
    }

    /**
     * Delete a CA Certificate (operationId `delete-ca_certificate`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/ca_certificates/{CACertificateId}', 'delete-ca_certificate', OperationScope::Both)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $id));
    }
}
