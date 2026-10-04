<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Transformation\GrpcWeb;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `grpc-web` plugin (gRPC-Web; doc `Transformation/grpc-web.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('Transformation/grpc-web.md', '#/properties/config')]
final readonly class GrpcWebConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'grpc-web';

    /**
     * @param string|null $allowOriginHeader The value of the `Access-Control-Allow-Origin` header in the response to the gRPC-Web client. Default: `*`.
     * @param bool|null   $passStrippedPath  If set to `true` causes the plugin to pass the stripped request path to the upstream gRPC service.
     * @param string|null $proto             If present, describes the gRPC types and methods.
     */
    public function __construct(
        public ?string $allowOriginHeader = null,
        public ?bool $passStrippedPath = null,
        public ?string $proto = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'allow_origin_header' => $this->allowOriginHeader,
            'pass_stripped_path' => $this->passStrippedPath,
            'proto' => $this->proto,
        ]);
    }
}
