<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Enums\HealthcheckType;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `healthchecks.active` object of Upstream (spec `Upstream.healthchecks.active`).
 */
#[Schema('#/components/schemas/Upstream/properties/healthchecks/properties/active')]
final readonly class UpstreamActiveHealthcheck implements Model
{
    /**
     * @param int|null                            $concurrency
     * @param array<array-key, list<string>>|null $headers                A map of header names to arrays of header values.
     * @param UpstreamActiveHealthy|null          $healthy
     * @param string|null                         $httpPath               A string representing a URL path, such as /path/to/resource.
     * @param string|null                         $httpsSni               A string representing an SNI (server name indication) value for TLS.
     * @param bool|null                           $httpsVerifyCertificate
     * @param float|null                          $timeout
     * @param HealthcheckType|null                $type
     * @param UpstreamActiveUnhealthy|null        $unhealthy
     */
    public function __construct(
        public ?int $concurrency = null,
        public ?array $headers = null,
        public ?UpstreamActiveHealthy $healthy = null,
        public ?string $httpPath = null,
        public ?string $httpsSni = null,
        public ?bool $httpsVerifyCertificate = null,
        public ?float $timeout = null,
        public ?HealthcheckType $type = null,
        public ?UpstreamActiveUnhealthy $unhealthy = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $healthy = Data::mapOrNull($data, 'healthy');
        $unhealthy = Data::mapOrNull($data, 'unhealthy');

        return new self(
            concurrency: Data::intOrNull($data, 'concurrency'),
            headers: Data::stringListMapOrNull($data, 'headers'),
            healthy: $healthy === null ? null : UpstreamActiveHealthy::fromArray($healthy),
            httpPath: Data::stringOrNull($data, 'http_path'),
            httpsSni: Data::stringOrNull($data, 'https_sni'),
            httpsVerifyCertificate: Data::boolOrNull($data, 'https_verify_certificate'),
            timeout: Data::floatOrNull($data, 'timeout'),
            type: Data::enumOrNull($data, 'type', HealthcheckType::class),
            unhealthy: $unhealthy === null ? null : UpstreamActiveUnhealthy::fromArray($unhealthy),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'concurrency' => $this->concurrency,
            'headers' => $this->headers,
            'healthy' => $this->healthy?->toArray(),
            'http_path' => $this->httpPath,
            'https_sni' => $this->httpsSni,
            'https_verify_certificate' => $this->httpsVerifyCertificate,
            'timeout' => $this->timeout,
            'type' => $this->type?->value,
            'unhealthy' => $this->unhealthy?->toArray(),
        ]);
    }
}
