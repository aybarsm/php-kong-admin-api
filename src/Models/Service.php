<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Enums\Protocol;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Aybarsm\Kong\AdminApi\Models\Shared\TlsSans;
use Override;

/**
 * A Service as returned by the Admin API (spec schema `Service`).
 *
 * The write-only `url` helper field is available on ServiceInput only.
 */
#[Schema('Service')]
final readonly class Service implements Model
{
    /**
     * @param string            $host              host of the upstream server (required)
     * @param list<string>|null $caCertificates    CA Certificate IDs used to verify the upstream TLS certificate
     * @param list<string>|null $tags              tags for grouping and filtering
     */
    public function __construct(
        public string $host,
        public ?string $id = null,
        public ?string $name = null,
        public ?Protocol $protocol = null,
        public ?int $port = null,
        public ?string $path = null,
        public ?int $retries = null,
        public ?int $connectTimeout = null,
        public ?int $writeTimeout = null,
        public ?int $readTimeout = null,
        public ?bool $enabled = null,
        public ?array $caCertificates = null,
        public ?ForeignKey $clientCertificate = null,
        public ?TlsSans $tlsSans = null,
        public ?bool $tlsVerify = null,
        public ?int $tlsVerifyDepth = null,
        public ?array $tags = null,
        public ?int $createdAt = null,
        public ?int $updatedAt = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $clientCertificate = Data::mapOrNull($data, 'client_certificate');
        $tlsSans = Data::mapOrNull($data, 'tls_sans');

        return new self(
            host: Data::string($data, 'host'),
            id: Data::stringOrNull($data, 'id'),
            name: Data::stringOrNull($data, 'name'),
            protocol: Data::enumOrNull($data, 'protocol', Protocol::class),
            port: Data::intOrNull($data, 'port'),
            path: Data::stringOrNull($data, 'path'),
            retries: Data::intOrNull($data, 'retries'),
            connectTimeout: Data::intOrNull($data, 'connect_timeout'),
            writeTimeout: Data::intOrNull($data, 'write_timeout'),
            readTimeout: Data::intOrNull($data, 'read_timeout'),
            enabled: Data::boolOrNull($data, 'enabled'),
            caCertificates: Data::stringListOrNull($data, 'ca_certificates'),
            clientCertificate: $clientCertificate === null ? null : ForeignKey::fromArray($clientCertificate),
            tlsSans: $tlsSans === null ? null : TlsSans::fromArray($tlsSans),
            tlsVerify: Data::boolOrNull($data, 'tls_verify'),
            tlsVerifyDepth: Data::intOrNull($data, 'tls_verify_depth'),
            tags: Data::stringListOrNull($data, 'tags'),
            createdAt: Data::intOrNull($data, 'created_at'),
            updatedAt: Data::intOrNull($data, 'updated_at'),
        );
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
            'retries' => $this->retries,
            'connect_timeout' => $this->connectTimeout,
            'write_timeout' => $this->writeTimeout,
            'read_timeout' => $this->readTimeout,
            'enabled' => $this->enabled,
            'ca_certificates' => $this->caCertificates,
            'client_certificate' => $this->clientCertificate?->toArray(),
            'tls_sans' => $this->tlsSans?->toArray(),
            'tls_verify' => $this->tlsVerify,
            'tls_verify_depth' => $this->tlsVerifyDepth,
            'tags' => $this->tags,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ]);
    }
}
