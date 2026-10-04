<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Zipkin;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `zipkin` plugin (Zipkin; doc `Monitoring/zipkin.md`).
 *
 * Read it from a returned Plugin with `ZipkinConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('Monitoring/zipkin.md', '#/properties/config')]
final readonly class ZipkinConfig implements PluginConfig
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'zipkin';

    /**
     * @param int|null                       $connectTimeout               An integer representing a timeout in milliseconds. Default: `2000`.
     * @param ZipkinDefaultHeaderType|null   $defaultHeaderType            Allows specifying the type of header to be added to requests with no pre-existing tracing headers and when `c… Default: `b3`.
     * @param string|null                    $defaultServiceName           Set a default service name to override `unknown-service-name` in the Zipkin spans.
     * @param ZipkinHeaderType|null          $headerType                   All HTTP requests going through the plugin are tagged with a tracing HTTP request. Default: `preserve`.
     * @param string|null                    $httpEndpoint                 A string representing a URL, such as https://example.com/path/to/resource?q=search.
     * @param string|null                    $httpResponseHeaderForTraceid
     * @param ZipkinHttpSpanName|null        $httpSpanName                 Specify whether to include the HTTP path in the span name. Default: `method`.
     * @param bool|null                      $includeCredential            Specify whether the credential of the currently authenticated consumer should be included in metadata sent to… Default: `true`.
     * @param string|null                    $localServiceName             The name of the service as displayed in Zipkin. Default: `kong`.
     * @param ZipkinPhaseDurationFlavor|null $phaseDurationFlavor          Specify whether to include the duration of each phase as an annotation or a tag. Default: `annotations`.
     * @param ZipkinPropagation|null         $propagation                  Default: `{"default_format": "b3"}`.
     * @param ZipkinQueue|null               $queue
     * @param int|null                       $readTimeout                  An integer representing a timeout in milliseconds. Default: `5000`.
     * @param float|null                     $sampleRatio                  How often to sample requests that do not contain trace IDs. Default: `0.001`.
     * @param int|null                       $sendTimeout                  An integer representing a timeout in milliseconds. Default: `5000`.
     * @param list<ZipkinStaticTags>|null    $staticTags                   The tags specified on this property will be added to the generated request traces.
     * @param string|null                    $tagsHeader                   The Zipkin plugin will add extra headers to the tags associated with any HTTP requests that come with a heade… Default: `Zipkin-Tags`.
     * @param ZipkinTraceidByteCount|null    $traceidByteCount             The length in bytes of each request's Trace ID. Default: `16`.
     */
    public function __construct(
        public ?int $connectTimeout = null,
        public ?ZipkinDefaultHeaderType $defaultHeaderType = null,
        public ?string $defaultServiceName = null,
        public ?ZipkinHeaderType $headerType = null,
        public ?string $httpEndpoint = null,
        public ?string $httpResponseHeaderForTraceid = null,
        public ?ZipkinHttpSpanName $httpSpanName = null,
        public ?bool $includeCredential = null,
        public ?string $localServiceName = null,
        public ?ZipkinPhaseDurationFlavor $phaseDurationFlavor = null,
        public ?ZipkinPropagation $propagation = null,
        public ?ZipkinQueue $queue = null,
        public ?int $readTimeout = null,
        public ?float $sampleRatio = null,
        public ?int $sendTimeout = null,
        public ?array $staticTags = null,
        public ?string $tagsHeader = null,
        public ?ZipkinTraceidByteCount $traceidByteCount = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $propagation = Data::mapOrNull($data, 'propagation');
        $queue = Data::mapOrNull($data, 'queue');
        $staticTags = Data::listOfMapsOrNull($data, 'static_tags');

        return new self(
            connectTimeout: Data::intOrNull($data, 'connect_timeout'),
            defaultHeaderType: Data::enumOrNull($data, 'default_header_type', ZipkinDefaultHeaderType::class),
            defaultServiceName: Data::stringOrNull($data, 'default_service_name'),
            headerType: Data::enumOrNull($data, 'header_type', ZipkinHeaderType::class),
            httpEndpoint: Data::stringOrNull($data, 'http_endpoint'),
            httpResponseHeaderForTraceid: Data::stringOrNull($data, 'http_response_header_for_traceid'),
            httpSpanName: Data::enumOrNull($data, 'http_span_name', ZipkinHttpSpanName::class),
            includeCredential: Data::boolOrNull($data, 'include_credential'),
            localServiceName: Data::stringOrNull($data, 'local_service_name'),
            phaseDurationFlavor: Data::enumOrNull($data, 'phase_duration_flavor', ZipkinPhaseDurationFlavor::class),
            propagation: $propagation === null ? null : ZipkinPropagation::fromArray($propagation),
            queue: $queue === null ? null : ZipkinQueue::fromArray($queue),
            readTimeout: Data::intOrNull($data, 'read_timeout'),
            sampleRatio: Data::floatOrNull($data, 'sample_ratio'),
            sendTimeout: Data::intOrNull($data, 'send_timeout'),
            staticTags: $staticTags === null ? null : array_map(ZipkinStaticTags::fromArray(...), $staticTags),
            tagsHeader: Data::stringOrNull($data, 'tags_header'),
            traceidByteCount: Data::enumOrNull($data, 'traceid_byte_count', ZipkinTraceidByteCount::class),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'connect_timeout' => $this->connectTimeout,
            'default_header_type' => $this->defaultHeaderType?->value,
            'default_service_name' => $this->defaultServiceName,
            'header_type' => $this->headerType?->value,
            'http_endpoint' => $this->httpEndpoint,
            'http_response_header_for_traceid' => $this->httpResponseHeaderForTraceid,
            'http_span_name' => $this->httpSpanName?->value,
            'include_credential' => $this->includeCredential,
            'local_service_name' => $this->localServiceName,
            'phase_duration_flavor' => $this->phaseDurationFlavor?->value,
            'propagation' => $this->propagation?->toArray(),
            'queue' => $this->queue?->toArray(),
            'read_timeout' => $this->readTimeout,
            'sample_ratio' => $this->sampleRatio,
            'send_timeout' => $this->sendTimeout,
            'static_tags' => Data::toArrays($this->staticTags),
            'tags_header' => $this->tagsHeader,
            'traceid_byte_count' => $this->traceidByteCount?->value,
        ]);
    }

    /**
     * Reads the typed configuration of a `zipkin` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `zipkin` plugin
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
