<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Transformation\GrpcWeb;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `grpc-web` plugin (gRPC-Web; doc `Transformation/grpc-web.md`).
 *
 * Read it from a returned Plugin with `GrpcWebConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('Transformation/grpc-web.md', '#/properties/config')]
final readonly class GrpcWebConfig implements PluginConfig
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
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            allowOriginHeader: Data::stringOrNull($data, 'allow_origin_header'),
            passStrippedPath: Data::boolOrNull($data, 'pass_stripped_path'),
            proto: Data::stringOrNull($data, 'proto'),
        );
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

    /**
     * Reads the typed configuration of a `grpc-web` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `grpc-web` plugin
     * @throws UnexpectedResponseException when its `config` does not match the plugin doc
     */
    #[Override]
    public static function fromPlugin(Plugin $plugin): static
    {
        if ($plugin->name !== self::NAME) {
            throw new InvalidArgumentException(sprintf('Expected a "%s" plugin, got "%s".', self::NAME, $plugin->name));
        }

        return self::fromArray(Data::asMap($plugin->config ?? [], 'config'));
    }
}
