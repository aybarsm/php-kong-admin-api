<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Transformation\CorrelationId;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `correlation-id` plugin (Correlation ID; doc `Transformation/correlation-id.md`).
 *
 * Read it from a returned Plugin with `CorrelationIdConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('Transformation/correlation-id.md', '#/properties/config')]
final readonly class CorrelationIdConfig implements PluginConfig
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'correlation-id';

    /**
     * @param bool|null                   $echoDownstream Whether to echo the header back to downstream (the client). Default: `false`.
     * @param CorrelationIdGenerator|null $generator      The generator to use for the correlation ID. Default: `uuid#counter`.
     * @param string|null                 $headerName     The HTTP header name to use for the correlation ID. Default: `Kong-Request-ID`.
     */
    public function __construct(
        public ?bool $echoDownstream = null,
        public ?CorrelationIdGenerator $generator = null,
        public ?string $headerName = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            echoDownstream: Data::boolOrNull($data, 'echo_downstream'),
            generator: Data::enumOrNull($data, 'generator', CorrelationIdGenerator::class),
            headerName: Data::stringOrNull($data, 'header_name'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'echo_downstream' => $this->echoDownstream,
            'generator' => $this->generator?->value,
            'header_name' => $this->headerName,
        ]);
    }

    /**
     * Reads the typed configuration of a `correlation-id` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `correlation-id` plugin
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
