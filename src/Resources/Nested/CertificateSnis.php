<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources\Nested;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\Sni;
use Aybarsm\Kong\AdminApi\Models\SniInput;
use Aybarsm\Kong\AdminApi\Pagination\ListOptions;
use Aybarsm\Kong\AdminApi\Pagination\Page;
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;
use Generator;

/**
 * SNIs nested under one Certificate: `/certificates/{CertificateId}/snis` and `/certificates/{CertificateId}/snis/{SNIIdOrName}`.
 *
 * Obtain it with `$client->certificates()->snis($certificateId)`.
 */
final readonly class CertificateSnis extends AbstractResource
{
    private const string SEGMENT = 'snis';

    /**
     * List one page of SNIs (operationId `list-sni-with-certificate`).
     *
     * @return Page<Sni>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/certificates/{CertificateId}/snis', 'list-sni-with-certificate', OperationScope::Both)]
    public function list(?ListOptions $options = null): Page
    {
        return $this->page($this->path(OperationScope::Both, self::SEGMENT), Sni::fromArray(...), $options);
    }

    /**
     * Lazily iterate every SNI across all pages (operationId `list-sni-with-certificate`).
     *
     * @return Generator<int, Sni>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/certificates/{CertificateId}/snis', 'list-sni-with-certificate', OperationScope::Both)]
    public function all(?ListOptions $options = null): Generator
    {
        return $this->walk($this->path(OperationScope::Both, self::SEGMENT), Sni::fromArray(...), $options);
    }

    /**
     * The page after $page, or null when $page is the last (operationId `list-sni-with-certificate`).
     *
     * Repeats list() with the page's `offset`, keeping the same filters and workspace. Kong's own `next`
     * link is not followed (it drops filters on nested lists; spec-notes Q6).
     *
     * @param Page<Sni> $page
     *
     * @return Page<Sni>|null
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/certificates/{CertificateId}/snis', 'list-sni-with-certificate', OperationScope::Both)]
    public function nextPage(Page $page, ?ListOptions $options = null): ?Page
    {
        return $page->offset === null ? null : $this->list(($options ?? new ListOptions())->withOffset($page->offset));
    }

    /**
     * Get an SNI by ID or name (operationId `get-sni-with-certificate`).
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_GET, '/certificates/{CertificateId}/snis/{SNIIdOrName}', 'get-sni-with-certificate', OperationScope::Both)]
    public function get(string $idOrName): Sni
    {
        return Sni::fromArray($this->object(
            Transport::METHOD_GET,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
        ));
    }

    /**
     * Create an SNI (operationId `create-sni-with-certificate`, body `SNIWithoutParents`).
     *
     * @param SniInput|array<string, mixed> $sni
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_POST, '/certificates/{CertificateId}/snis', 'create-sni-with-certificate', OperationScope::Both)]
    public function create(SniInput|array $sni): Sni
    {
        return Sni::fromArray($this->object(
            Transport::METHOD_POST,
            $this->path(OperationScope::Both, self::SEGMENT),
            body: $sni,
        ));
    }

    /**
     * Update fields of an SNI (operationId `update-sni-with-certificate`, PATCH, body `SNI`).
     *
     * @param SniInput|array<string, mixed> $sni only the fields to change
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PATCH, '/certificates/{CertificateId}/snis/{SNIIdOrName}', 'update-sni-with-certificate', OperationScope::Both)]
    public function update(string $idOrName, SniInput|array $sni): Sni
    {
        return Sni::fromArray($this->object(
            Transport::METHOD_PATCH,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
            body: $sni,
        ));
    }

    /**
     * Create or replace an SNI by ID or name (operationId `upsert-sni-with-certificate`, PUT, body `SNIWithoutParents`).
     *
     * @param SniInput|array<string, mixed> $sni
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_PUT, '/certificates/{CertificateId}/snis/{SNIIdOrName}', 'upsert-sni-with-certificate', OperationScope::Both)]
    public function upsert(string $idOrName, SniInput|array $sni): Sni
    {
        return Sni::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::Both, self::SEGMENT, $idOrName),
            body: $sni,
        ));
    }

    /**
     * Delete an SNI (operationId `delete-sni-with-certificate`). Kong answers 204 whether or not it existed.
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $idOrName is empty
     */
    #[Operation(Transport::METHOD_DELETE, '/certificates/{CertificateId}/snis/{SNIIdOrName}', 'delete-sni-with-certificate', OperationScope::Both)]
    public function delete(string $idOrName): void
    {
        $this->none(Transport::METHOD_DELETE, $this->path(OperationScope::Both, self::SEGMENT, $idOrName));
    }
}
