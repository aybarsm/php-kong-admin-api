<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Enums\HttpsRedirectStatusCode;
use Aybarsm\Kong\AdminApi\Enums\PathHandling;
use Aybarsm\Kong\AdminApi\Enums\Protocol;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * A Route matched by a Router Expression (spec schema `RouteExpression`); only available when Kong's
 * `router_flavor` is `expressions`.
 */
#[Schema('RouteExpression')]
final readonly class RouteExpression implements Route
{
    /**
     * @param string|null         $expression the Router Expression used to match requests
     * @param int|null            $priority   matching order for expression routes; higher matches first
     * @param list<Protocol>|null $protocols  protocols this Route accepts
     * @param list<string>|null   $tags       tags for grouping and filtering
     */
    public function __construct(
        public ?string $expression = null,
        public ?string $id = null,
        public ?string $name = null,
        public ?int $priority = null,
        public ?array $protocols = null,
        public ?HttpsRedirectStatusCode $httpsRedirectStatusCode = null,
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

        return new self(
            expression: Data::stringOrNull($data, 'expression'),
            id: Data::stringOrNull($data, 'id'),
            name: Data::stringOrNull($data, 'name'),
            priority: Data::intOrNull($data, 'priority'),
            protocols: Data::enumListOrNull($data, 'protocols', Protocol::class),
            httpsRedirectStatusCode: Data::enumOrNull($data, 'https_redirect_status_code', HttpsRedirectStatusCode::class),
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
            'expression' => $this->expression,
            'id' => $this->id,
            'name' => $this->name,
            'priority' => $this->priority,
            'protocols' => Data::enumValues($this->protocols),
            'https_redirect_status_code' => $this->httpsRedirectStatusCode?->value,
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
