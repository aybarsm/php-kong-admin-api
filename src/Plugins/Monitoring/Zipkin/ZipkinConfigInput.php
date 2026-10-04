<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Zipkin;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `zipkin` plugin (Zipkin; doc `Monitoring/zipkin.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('Monitoring/zipkin.md', '#/properties/config')]
final readonly class ZipkinConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'zipkin';

    /**
     * @param int|null                 $connectTimeout               An integer representing a timeout in milliseconds. Default: `2000`.
     * @param DefaultHeaderType|null   $defaultHeaderType            Allows specifying the type of header to be added to requests with no pre-existing tracing headers and when `c… Default: `b3`.
     * @param string|null              $defaultServiceName           Set a default service name to override `unknown-service-name` in the Zipkin spans.
     * @param HeaderType|null          $headerType                   All HTTP requests going through the plugin are tagged with a tracing HTTP request. Default: `preserve`.
     * @param string|null              $httpEndpoint                 A string representing a URL, such as https://example.com/path/to/resource?q=search.
     * @param string|null              $httpResponseHeaderForTraceid
     * @param HttpSpanName|null        $httpSpanName                 Specify whether to include the HTTP path in the span name. Default: `method`.
     * @param bool|null                $includeCredential            Specify whether the credential of the currently authenticated consumer should be included in metadata sent to… Default: `true`.
     * @param string|null              $localServiceName             The name of the service as displayed in Zipkin. Default: `kong`.
     * @param PhaseDurationFlavor|null $phaseDurationFlavor          Specify whether to include the duration of each phase as an annotation or a tag. Default: `annotations`.
     * @param Propagation|null         $propagation                  Default: `{"default_format": "b3"}`.
     * @param Queue|null               $queue
     * @param int|null                 $readTimeout                  An integer representing a timeout in milliseconds. Default: `5000`.
     * @param int|float|null           $sampleRatio                  How often to sample requests that do not contain trace IDs. Default: `0.001`.
     * @param int|null                 $sendTimeout                  An integer representing a timeout in milliseconds. Default: `5000`.
     * @param list<StaticTags>|null    $staticTags                   The tags specified on this property will be added to the generated request traces.
     * @param string|null              $tagsHeader                   The Zipkin plugin will add extra headers to the tags associated with any HTTP requests that come with a heade… Default: `Zipkin-Tags`.
     * @param TraceidByteCount|null    $traceidByteCount             The length in bytes of each request's Trace ID. Default: `16`.
     */
    public function __construct(
        public ?int $connectTimeout = null,
        public ?DefaultHeaderType $defaultHeaderType = null,
        public ?string $defaultServiceName = null,
        public ?HeaderType $headerType = null,
        public ?string $httpEndpoint = null,
        public ?string $httpResponseHeaderForTraceid = null,
        public ?HttpSpanName $httpSpanName = null,
        public ?bool $includeCredential = null,
        public ?string $localServiceName = null,
        public ?PhaseDurationFlavor $phaseDurationFlavor = null,
        public ?Propagation $propagation = null,
        public ?Queue $queue = null,
        public ?int $readTimeout = null,
        public int|float|null $sampleRatio = null,
        public ?int $sendTimeout = null,
        public ?array $staticTags = null,
        public ?string $tagsHeader = null,
        public ?TraceidByteCount $traceidByteCount = null,
    ) {
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
}
