<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Transformation\CorrelationId;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `correlation-id` plugin (Correlation ID; doc `Transformation/correlation-id.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('Transformation/correlation-id.md', '#/properties/config')]
final readonly class CorrelationIdConfigInput implements Input
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
}
