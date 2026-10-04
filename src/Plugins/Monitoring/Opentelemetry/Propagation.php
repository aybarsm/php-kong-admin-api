<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Opentelemetry;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.propagation` object of the OpenTelemetry plugin (doc `Monitoring/opentelemetry.md`).
 */
#[PluginSchema('Monitoring/opentelemetry.md', '#/properties/config/properties/propagation')]
final readonly class Propagation implements Model
{
    /**
     * @param list<string>|null             $clear         Header names to clear after context extraction.
     * @param PropagationDefaultFormat|null $defaultFormat The default header format to use when extractors did not match any format in the incoming headers and `inject… Default: `w3c`.
     * @param list<PropagationExtract>|null $extract       Header formats used to extract tracing context from incoming requests.
     * @param list<PropagationInject>|null  $inject        Header formats used to inject tracing context.
     */
    public function __construct(
        public ?array $clear = null,
        public ?PropagationDefaultFormat $defaultFormat = null,
        public ?array $extract = null,
        public ?array $inject = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            clear: Data::stringListOrNull($data, 'clear'),
            defaultFormat: Data::enumOrNull($data, 'default_format', PropagationDefaultFormat::class),
            extract: Data::enumListOrNull($data, 'extract', PropagationExtract::class),
            inject: Data::enumListOrNull($data, 'inject', PropagationInject::class),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'clear' => $this->clear,
            'default_format' => $this->defaultFormat?->value,
            'extract' => Data::enumValues($this->extract),
            'inject' => Data::enumValues($this->inject),
        ]);
    }
}
