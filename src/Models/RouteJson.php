<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Enums\HttpsRedirectStatusCode;
use Aybarsm\Kong\AdminApi\Enums\PathHandling;
use Aybarsm\Kong\AdminApi\Enums\Protocol;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Aybarsm\Kong\AdminApi\Models\Shared\IpPort;
use Override;

/**
 * A traditional Route matched by paths, hosts, methods, headers, SNIs or L4 addresses (spec schema `RouteJson`).
 */
#[Schema('RouteJson')]
final readonly class RouteJson implements Route
{
    /**
     * @param list<IpPort>|null                    $destinations L4 destination IP/port pairs
     * @param array<array-key, list<string>>|null  $headers      header name to accepted values
     * @param list<string>|null                    $hosts        domain names (case sensitive)
     * @param list<string>|null                    $methods      HTTP methods
     * @param list<string>|null                    $paths        paths (`/fixed` or `~/regex`)
     * @param list<Protocol>|null                  $protocols    protocols this Route accepts
     * @param list<string>|null                    $snis         SNIs matched on TLS routes
     * @param list<IpPort>|null                    $sources      L4 source IP/port pairs
     * @param list<string>|null                    $tags         tags for grouping and filtering
     */
    public function __construct(
        public ?string $id = null,
        public ?string $name = null,
        public ?array $protocols = null,
        public ?array $methods = null,
        public ?array $hosts = null,
        public ?array $paths = null,
        public ?array $headers = null,
        public ?array $snis = null,
        public ?array $sources = null,
        public ?array $destinations = null,
        public ?HttpsRedirectStatusCode $httpsRedirectStatusCode = null,
        public ?int $regexPriority = null,
        public ?bool $stripPath = null,
        public ?PathHandling $pathHandling = null,
        public ?bool $preserveHost = null,
        public ?bool $requestBuffering = null,
        public ?bool $responseBuffering = null,
        public ?ForeignKey $service = null,
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
        $service = Data::mapOrNull($data, 'service');
        $sources = Data::listOfMapsOrNull($data, 'sources');
        $destinations = Data::listOfMapsOrNull($data, 'destinations');

        return new self(
            id: Data::stringOrNull($data, 'id'),
            name: Data::stringOrNull($data, 'name'),
            protocols: Data::enumListOrNull($data, 'protocols', Protocol::class),
            methods: Data::stringListOrNull($data, 'methods'),
            hosts: Data::stringListOrNull($data, 'hosts'),
            paths: Data::stringListOrNull($data, 'paths'),
            headers: Data::stringListMapOrNull($data, 'headers'),
            snis: Data::stringListOrNull($data, 'snis'),
            sources: $sources === null ? null : array_map(IpPort::fromArray(...), $sources),
            destinations: $destinations === null ? null : array_map(IpPort::fromArray(...), $destinations),
            httpsRedirectStatusCode: Data::enumOrNull($data, 'https_redirect_status_code', HttpsRedirectStatusCode::class),
            regexPriority: Data::intOrNull($data, 'regex_priority'),
            stripPath: Data::boolOrNull($data, 'strip_path'),
            pathHandling: Data::enumOrNull($data, 'path_handling', PathHandling::class),
            preserveHost: Data::boolOrNull($data, 'preserve_host'),
            requestBuffering: Data::boolOrNull($data, 'request_buffering'),
            responseBuffering: Data::boolOrNull($data, 'response_buffering'),
            service: $service === null ? null : ForeignKey::fromArray($service),
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
            'id' => $this->id,
            'name' => $this->name,
            'protocols' => Data::enumValues($this->protocols),
            'methods' => $this->methods,
            'hosts' => $this->hosts,
            'paths' => $this->paths,
            'headers' => $this->headers,
            'snis' => $this->snis,
            'sources' => Data::toArrays($this->sources),
            'destinations' => Data::toArrays($this->destinations),
            'https_redirect_status_code' => $this->httpsRedirectStatusCode?->value,
            'regex_priority' => $this->regexPriority,
            'strip_path' => $this->stripPath,
            'path_handling' => $this->pathHandling?->value,
            'preserve_host' => $this->preserveHost,
            'request_buffering' => $this->requestBuffering,
            'response_buffering' => $this->responseBuffering,
            'service' => $this->service?->toArray(),
            'tags' => $this->tags,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ]);
    }
}
