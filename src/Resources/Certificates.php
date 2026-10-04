<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\Certificate;
use Aybarsm\Kong\AdminApi\Models\CertificateInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\Nested\CertificateSnis;
use Generator;

/**
 * Certificates (spec tag "Certificates"): `/certificates` and `/certificates/{CertificateId}`.
 */
final readonly class Certificates extends AbstractResource
{
    private const string SEGMENT = 'certificates';

    /**
     * List one page of Certificates (operationId `list-certificate`).
     *
     * @return Page<Certificate>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/certificates', 'list-certificate', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), Certificate::fromArray(...), $options);
    }

    /**
     * Lazily iterate every Certificate across all pages (operationId `list-certificate`).
     *
     * @return Generator<int, Certificate>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/certificates', 'list-certificate', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), Certificate::fromArray(...), $options);
    }

    /**
     * Get a Certificate by ID (operationId `get-certificate`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_GET, '/certificates/{CertificateId}', 'get-certificate', OperationScope::Both)]
    public function get(string $id): Certificate
    {
        return Certificate::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
        ));
    }

    /**
     * Create a Certificate (operationId `create-certificate`, body `Certificate`).
     *
     * @param CertificateInput|array<string, mixed> $certificate
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/certificates', 'create-certificate', OperationScope::Both)]
    public function create(CertificateInput|array $certificate): Certificate
    {
        return Certificate::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $certificate,
        ));
    }

    /**
     * Update fields of a Certificate (operationId `update-certificate`, PATCH, body `Certificate`).
     *
     * @param CertificateInput|array<string, mixed> $certificate only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/certificates/{CertificateId}', 'update-certificate', OperationScope::Both)]
    public function update(string $id, CertificateInput|array $certificate): Certificate
    {
        return Certificate::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $certificate,
        ));
    }

    /**
     * Create or replace a Certificate by ID (operationId `upsert-certificate`, PUT, body `Certificate`).
     *
     * @param CertificateInput|array<string, mixed> $certificate
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_PUT, '/certificates/{CertificateId}', 'upsert-certificate', OperationScope::Both)]
    public function upsert(string $id, CertificateInput|array $certificate): Certificate
    {
        return Certificate::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $id),
            body: $certificate,
        ));
    }

    /**
     * Delete a Certificate (operationId `delete-certificate`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $id is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/certificates/{CertificateId}', 'delete-certificate', OperationScope::Both)]
    public function delete(string $id): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $id));
    }

    /**
     * SNIs of one Certificate: `/certificates/{CertificateId}/snis`.
     *
     * @throws InvalidArgumentException when $certificateId is empty
     */
    public function snis(string $certificateId): CertificateSnis
    {
        if ($certificateId === '') {
            throw new InvalidArgumentException('Certificate ID must not be empty.');
        }

        return new CertificateSnis($this->transport, [...$this->parent, self::SEGMENT, $certificateId]);
    }
}
