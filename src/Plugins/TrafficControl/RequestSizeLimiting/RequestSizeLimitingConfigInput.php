<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\RequestSizeLimiting;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `request-size-limiting` plugin (Request Size Limiting; doc `TrafficControl/request-size-limiting.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('TrafficControl/request-size-limiting.md', '#/properties/config')]
final readonly class RequestSizeLimitingConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'request-size-limiting';

    /**
     * @param int|null                         $allowedPayloadSize   Allowed request payload size in megabytes. Default: `128`.
     * @param bool|null                        $requireContentLength Set to `true` to ensure a valid `Content-Length` header exists before reading the request body. Default: `false`.
     * @param RequestSizeLimitingSizeUnit|null $sizeUnit             Size unit can be set either in `bytes`, `kilobytes`, or `megabytes` (default). Default: `megabytes`.
     */
    public function __construct(
        public ?int $allowedPayloadSize = null,
        public ?bool $requireContentLength = null,
        public ?RequestSizeLimitingSizeUnit $sizeUnit = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'allowed_payload_size' => $this->allowedPayloadSize,
            'require_content_length' => $this->requireContentLength,
            'size_unit' => $this->sizeUnit?->value,
        ]);
    }
}
