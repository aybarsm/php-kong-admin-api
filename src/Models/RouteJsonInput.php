<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Enums\HttpsRedirectStatusCode;
use Aybarsm\Kong\AdminApi\Enums\PathHandling;
use Aybarsm\Kong\AdminApi\Enums\Protocol;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Aybarsm\Kong\AdminApi\Models\Shared\IpPort;
use Override;

/**
 * Request body for creating, updating or upserting a traditional Route (spec schema `RouteJson`; nested
 * Service routes use `RouteWithoutParents`, the same `oneOf`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('RouteJson')]
final readonly class RouteJsonInput implements Input
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
     * @param ForeignKey|string|null               $service      the Service this Route proxies to (reference or ID)
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
        public ForeignKey|string|null $service = null,
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
            'service' => $this->service === null ? null : ForeignKey::of($this->service)->toArray(),
            'tags' => $this->tags,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ]);
    }
}
