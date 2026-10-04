<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Enums\Protocol;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Aybarsm\Kong\AdminApi\Models\Shared\TlsSans;
use Override;

/**
 * Request body for creating, updating or upserting a Service (spec schema `Service`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null (e.g. to clear `ca_certificates`), pass an array instead.
 */
#[Schema('Service')]
final readonly class ServiceInput implements Input
{
    /**
     * @param string|null                   $host              required on create unless `url` is given
     * @param string|null                   $url               write-only helper that sets protocol, host, port and path
     * @param list<string>|null             $caCertificates    CA Certificate IDs
     * @param ForeignKey|string|null        $clientCertificate Certificate reference or ID
     * @param list<string>|null             $tags              tags for grouping and filtering
     */
    public function __construct(
        public ?string $host = null,
        public ?string $id = null,
        public ?string $name = null,
        public ?Protocol $protocol = null,
        public ?int $port = null,
        public ?string $path = null,
        public ?string $url = null,
        public ?int $retries = null,
        public ?int $connectTimeout = null,
        public ?int $writeTimeout = null,
        public ?int $readTimeout = null,
        public ?bool $enabled = null,
        public ?array $caCertificates = null,
        public ForeignKey|string|null $clientCertificate = null,
        public ?TlsSans $tlsSans = null,
        public ?bool $tlsVerify = null,
        public ?int $tlsVerifyDepth = null,
        public ?array $tags = null,
        public ?int $createdAt = null,
        public ?int $updatedAt = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'host' => $this->host,
            'id' => $this->id,
            'name' => $this->name,
            'protocol' => $this->protocol?->value,
            'port' => $this->port,
            'path' => $this->path,
            'url' => $this->url,
            'retries' => $this->retries,
            'connect_timeout' => $this->connectTimeout,
            'write_timeout' => $this->writeTimeout,
            'read_timeout' => $this->readTimeout,
            'enabled' => $this->enabled,
            'ca_certificates' => $this->caCertificates,
            'client_certificate' => $this->clientCertificate === null ? null : ForeignKey::of($this->clientCertificate)->toArray(),
            'tls_sans' => $this->tlsSans?->toArray(),
            'tls_verify' => $this->tlsVerify,
            'tls_verify_depth' => $this->tlsVerifyDepth,
            'tags' => $this->tags,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ]);
    }
}
