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
use Override;

/**
 * Request body for creating, updating or upserting an expression Route (spec schema `RouteExpression`; nested
 * Service routes use `RouteWithoutParents`, the same `oneOf`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('RouteExpression')]
final readonly class RouteExpressionInput implements Input
{
    /**
     * @param string|null         $expression the Router Expression used to match requests
     * @param int|null            $priority   matching order for expression routes; higher matches first
     * @param list<Protocol>|null $protocols  protocols this Route accepts
     * @param list<string>|null   $tags       tags for grouping and filtering
     * @param ForeignKey|string|null $service the Service this Route proxies to (reference or ID)
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
            'service' => $this->service === null ? null : ForeignKey::of($this->service)->toArray(),
            'tags' => $this->tags,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ]);
    }
}
