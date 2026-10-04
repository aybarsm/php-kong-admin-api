<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Zipkin;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.propagation` object of the Zipkin plugin (doc `Monitoring/zipkin.md`).
 */
#[PluginSchema('Monitoring/zipkin.md', '#/properties/config/properties/propagation')]
final readonly class ZipkinPropagation implements Model
{
    /**
     * @param list<string>|null                   $clear         Header names to clear after context extraction.
     * @param ZipkinPropagationDefaultFormat|null $defaultFormat The default header format to use when extractors did not match any format in the incoming headers and `inject… Default: `b3`.
     * @param list<ZipkinPropagationExtract>|null $extract       Header formats used to extract tracing context from incoming requests.
     * @param list<ZipkinPropagationInject>|null  $inject        Header formats used to inject tracing context.
     */
    public function __construct(
        public ?array $clear = null,
        public ?ZipkinPropagationDefaultFormat $defaultFormat = null,
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
            defaultFormat: Data::enumOrNull($data, 'default_format', ZipkinPropagationDefaultFormat::class),
            extract: Data::enumListOrNull($data, 'extract', ZipkinPropagationExtract::class),
            inject: Data::enumListOrNull($data, 'inject', ZipkinPropagationInject::class),
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
