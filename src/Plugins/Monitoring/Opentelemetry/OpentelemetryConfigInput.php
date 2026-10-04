<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Opentelemetry;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `opentelemetry` plugin (OpenTelemetry; doc `Monitoring/opentelemetry.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('Monitoring/opentelemetry.md', '#/properties/config')]
final readonly class OpentelemetryConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'opentelemetry';

    /**
     * @param OpentelemetryAccessLogs|null       $accessLogs                   Configuration for exporting access logs to an OTLP/HTTP endpoint.
     * @param int|null                           $batchFlushDelay              The delay, in seconds, between two consecutive batches.
     * @param int|null                           $batchSpanCount               The number of spans to be sent in a single batch.
     * @param int|null                           $connectTimeout               An integer representing a timeout in milliseconds. Default: `1000`.
     * @param OpentelemetryHeaderType|null       $headerType                   Default: `preserve`.
     * @param array<array-key, string>|null      $headers                      The custom headers to be added in the HTTP request sent to the OTLP server.
     * @param string|null                        $httpResponseHeaderForTraceid
     * @param string|null                        $logsEndpoint                 An HTTP URL endpoint where internal logs are exported.
     * @param OpentelemetryMetrics|null          $metrics                      Configuration for exporting metrics to an OTLP/HTTP endpoint.
     * @param OpentelemetryPropagation|null      $propagation                  Default: `{"default_format": "w3c"}`.
     * @param OpentelemetryQueue|null            $queue                        Default: `{"max_batch_size": 200}`.
     * @param int|null                           $readTimeout                  An integer representing a timeout in milliseconds. Default: `5000`.
     * @param array<array-key, string>|null      $resourceAttributes           A key-value map of resource attributes to be sent with the telemetry data.
     * @param float|null                         $samplingRate                 Tracing sampling rate for configuring the probability-based sampler.
     * @param OpentelemetrySamplingStrategy|null $samplingStrategy             The sampling strategy to use for OTLP `traces`. Default: `parent_drop_probability_fallback`.
     * @param int|null                           $sendTimeout                  An integer representing a timeout in milliseconds. Default: `5000`.
     * @param string|null                        $tracesEndpoint               A string representing a URL, such as https://example.com/path/to/resource?q=search.
     */
    public function __construct(
        public ?OpentelemetryAccessLogs $accessLogs = null,
        public ?int $batchFlushDelay = null,
        public ?int $batchSpanCount = null,
        public ?int $connectTimeout = null,
        public ?OpentelemetryHeaderType $headerType = null,
        public ?array $headers = null,
        public ?string $httpResponseHeaderForTraceid = null,
        public ?string $logsEndpoint = null,
        public ?OpentelemetryMetrics $metrics = null,
        public ?OpentelemetryPropagation $propagation = null,
        public ?OpentelemetryQueue $queue = null,
        public ?int $readTimeout = null,
        public ?array $resourceAttributes = null,
        public ?float $samplingRate = null,
        public ?OpentelemetrySamplingStrategy $samplingStrategy = null,
        public ?int $sendTimeout = null,
        public ?string $tracesEndpoint = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'access_logs' => $this->accessLogs?->toArray(),
            'batch_flush_delay' => $this->batchFlushDelay,
            'batch_span_count' => $this->batchSpanCount,
            'connect_timeout' => $this->connectTimeout,
            'header_type' => $this->headerType?->value,
            'headers' => $this->headers,
            'http_response_header_for_traceid' => $this->httpResponseHeaderForTraceid,
            'logs_endpoint' => $this->logsEndpoint,
            'metrics' => $this->metrics?->toArray(),
            'propagation' => $this->propagation?->toArray(),
            'queue' => $this->queue?->toArray(),
            'read_timeout' => $this->readTimeout,
            'resource_attributes' => $this->resourceAttributes,
            'sampling_rate' => $this->samplingRate,
            'sampling_strategy' => $this->samplingStrategy?->value,
            'send_timeout' => $this->sendTimeout,
            'traces_endpoint' => $this->tracesEndpoint,
        ]);
    }
}
