<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Transformation\GrpcGateway;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `grpc-gateway` plugin (gRPC-Gateway; doc `Transformation/grpc-gateway.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('Transformation/grpc-gateway.md', '#/properties/config')]
final readonly class GrpcGatewayConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'grpc-gateway';

    /**
     * @param string|null $proto Describes the gRPC types and methods.
     */
    public function __construct(
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
            'proto' => $this->proto,
        ]);
    }
}
