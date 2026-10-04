<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Opentelemetry;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `opentelemetry` plugin (OpenTelemetry; doc `Monitoring/opentelemetry.md`).
 *
 * Read it from a returned Plugin with `OpentelemetryConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('Monitoring/opentelemetry.md', '#/properties/config')]
final readonly class OpentelemetryConfig implements PluginConfig
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
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $accessLogs = Data::mapOrNull($data, 'access_logs');
        $metrics = Data::mapOrNull($data, 'metrics');
        $propagation = Data::mapOrNull($data, 'propagation');
        $queue = Data::mapOrNull($data, 'queue');

        return new self(
            accessLogs: $accessLogs === null ? null : OpentelemetryAccessLogs::fromArray($accessLogs),
            batchFlushDelay: Data::intOrNull($data, 'batch_flush_delay'),
            batchSpanCount: Data::intOrNull($data, 'batch_span_count'),
            connectTimeout: Data::intOrNull($data, 'connect_timeout'),
            headerType: Data::enumOrNull($data, 'header_type', OpentelemetryHeaderType::class),
            headers: Data::stringMapOrNull($data, 'headers'),
            httpResponseHeaderForTraceid: Data::stringOrNull($data, 'http_response_header_for_traceid'),
            logsEndpoint: Data::stringOrNull($data, 'logs_endpoint'),
            metrics: $metrics === null ? null : OpentelemetryMetrics::fromArray($metrics),
            propagation: $propagation === null ? null : OpentelemetryPropagation::fromArray($propagation),
            queue: $queue === null ? null : OpentelemetryQueue::fromArray($queue),
            readTimeout: Data::intOrNull($data, 'read_timeout'),
            resourceAttributes: Data::stringMapOrNull($data, 'resource_attributes'),
            samplingRate: Data::floatOrNull($data, 'sampling_rate'),
            samplingStrategy: Data::enumOrNull($data, 'sampling_strategy', OpentelemetrySamplingStrategy::class),
            sendTimeout: Data::intOrNull($data, 'send_timeout'),
            tracesEndpoint: Data::stringOrNull($data, 'traces_endpoint'),
        );
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

    /**
     * Reads the typed configuration of a `opentelemetry` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `opentelemetry` plugin
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
