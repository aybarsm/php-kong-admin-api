<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Enums\UpstreamAlgorithm;
use Aybarsm\Kong\AdminApi\Enums\UpstreamHashOn;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * An Upstream (load-balancing target group) as returned by the Admin API (spec schema `Upstream`).
 */
#[Schema('Upstream')]
final readonly class Upstream implements Model
{
    /**
     * @param string                    $name                     This is a hostname, which must be equal to the `host` of a Service.
     * @param UpstreamAlgorithm|null    $algorithm                Which load balancing algorithm to use.
     * @param ForeignKey|null           $clientCertificate        If set, the certificate to be used as client certificate while TLS handshaking to the upstream server.
     * @param int|null                  $createdAt                Unix epoch when the resource was created.
     * @param UpstreamHashOn|null       $hashFallback             What to use as hashing input if the primary `hash_on` does not return a hash (eg.
     * @param string|null               $hashFallbackHeader       The header name to take the value from as hash input.
     * @param string|null               $hashFallbackQueryArg     The name of the query string argument to take the value from as hash input.
     * @param string|null               $hashFallbackUriCapture   The name of the route URI capture to take the value from as hash input.
     * @param UpstreamHashOn|null       $hashOn                   What to use as hashing input.
     * @param string|null               $hashOnCookie             The cookie name to take the value from as hash input.
     * @param string|null               $hashOnCookiePath         The cookie path to set in the response headers.
     * @param string|null               $hashOnHeader             The header name to take the value from as hash input.
     * @param string|null               $hashOnQueryArg           The name of the query string argument to take the value from as hash input.
     * @param string|null               $hashOnUriCapture         The name of the route URI capture to take the value from as hash input.
     * @param UpstreamHealthchecks|null $healthchecks             The array of healthchecks.
     * @param string|null               $hostHeader               The hostname to be used as `Host` header when proxying requests through Kong.
     * @param string|null               $id                       A string representing a UUID (universally unique identifier).
     * @param int|null                  $slots                    The number of slots in the load balancer algorithm.
     * @param string|null               $stickySessionsCookie     The cookie name to keep sticky sessions.
     * @param string|null               $stickySessionsCookiePath A string representing a URL path, such as /path/to/resource.
     * @param list<string>|null         $tags                     An optional set of strings associated with the Upstream for grouping and filtering.
     * @param int|null                  $updatedAt                Unix epoch when the resource was last updated.
     * @param bool|null                 $useSrvName               If set, the balancer will use SRV hostname(if DNS Answer has SRV record) as the proxy upstream `Host`.
     */
    public function __construct(
        public string $name,
        public ?UpstreamAlgorithm $algorithm = null,
        public ?ForeignKey $clientCertificate = null,
        public ?int $createdAt = null,
        public ?UpstreamHashOn $hashFallback = null,
        public ?string $hashFallbackHeader = null,
        public ?string $hashFallbackQueryArg = null,
        public ?string $hashFallbackUriCapture = null,
        public ?UpstreamHashOn $hashOn = null,
        public ?string $hashOnCookie = null,
        public ?string $hashOnCookiePath = null,
        public ?string $hashOnHeader = null,
        public ?string $hashOnQueryArg = null,
        public ?string $hashOnUriCapture = null,
        public ?UpstreamHealthchecks $healthchecks = null,
        public ?string $hostHeader = null,
        public ?string $id = null,
        public ?int $slots = null,
        public ?string $stickySessionsCookie = null,
        public ?string $stickySessionsCookiePath = null,
        public ?array $tags = null,
        public ?int $updatedAt = null,
        public ?bool $useSrvName = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $clientCertificate = Data::mapOrNull($data, 'client_certificate');
        $healthchecks = Data::mapOrNull($data, 'healthchecks');

        return new self(
            name: Data::string($data, 'name'),
            algorithm: Data::enumOrNull($data, 'algorithm', UpstreamAlgorithm::class),
            clientCertificate: $clientCertificate === null ? null : ForeignKey::fromArray($clientCertificate),
            createdAt: Data::intOrNull($data, 'created_at'),
            hashFallback: Data::enumOrNull($data, 'hash_fallback', UpstreamHashOn::class),
            hashFallbackHeader: Data::stringOrNull($data, 'hash_fallback_header'),
            hashFallbackQueryArg: Data::stringOrNull($data, 'hash_fallback_query_arg'),
            hashFallbackUriCapture: Data::stringOrNull($data, 'hash_fallback_uri_capture'),
            hashOn: Data::enumOrNull($data, 'hash_on', UpstreamHashOn::class),
            hashOnCookie: Data::stringOrNull($data, 'hash_on_cookie'),
            hashOnCookiePath: Data::stringOrNull($data, 'hash_on_cookie_path'),
            hashOnHeader: Data::stringOrNull($data, 'hash_on_header'),
            hashOnQueryArg: Data::stringOrNull($data, 'hash_on_query_arg'),
            hashOnUriCapture: Data::stringOrNull($data, 'hash_on_uri_capture'),
            healthchecks: $healthchecks === null ? null : UpstreamHealthchecks::fromArray($healthchecks),
            hostHeader: Data::stringOrNull($data, 'host_header'),
            id: Data::stringOrNull($data, 'id'),
            slots: Data::intOrNull($data, 'slots'),
            stickySessionsCookie: Data::stringOrNull($data, 'sticky_sessions_cookie'),
            stickySessionsCookiePath: Data::stringOrNull($data, 'sticky_sessions_cookie_path'),
            tags: Data::stringListOrNull($data, 'tags'),
            updatedAt: Data::intOrNull($data, 'updated_at'),
            useSrvName: Data::boolOrNull($data, 'use_srv_name'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'name' => $this->name,
            'algorithm' => $this->algorithm?->value,
            'client_certificate' => $this->clientCertificate?->toArray(),
            'created_at' => $this->createdAt,
            'hash_fallback' => $this->hashFallback?->value,
            'hash_fallback_header' => $this->hashFallbackHeader,
            'hash_fallback_query_arg' => $this->hashFallbackQueryArg,
            'hash_fallback_uri_capture' => $this->hashFallbackUriCapture,
            'hash_on' => $this->hashOn?->value,
            'hash_on_cookie' => $this->hashOnCookie,
            'hash_on_cookie_path' => $this->hashOnCookiePath,
            'hash_on_header' => $this->hashOnHeader,
            'hash_on_query_arg' => $this->hashOnQueryArg,
            'hash_on_uri_capture' => $this->hashOnUriCapture,
            'healthchecks' => $this->healthchecks?->toArray(),
            'host_header' => $this->hostHeader,
            'id' => $this->id,
            'slots' => $this->slots,
            'sticky_sessions_cookie' => $this->stickySessionsCookie,
            'sticky_sessions_cookie_path' => $this->stickySessionsCookiePath,
            'tags' => $this->tags,
            'updated_at' => $this->updatedAt,
            'use_srv_name' => $this->useSrvName,
        ]);
    }
}
