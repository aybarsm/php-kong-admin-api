<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Opentelemetry;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.metrics` object of the OpenTelemetry plugin (doc `Monitoring/opentelemetry.md`).
 */
#[PluginSchema('Monitoring/opentelemetry.md', '#/properties/config/properties/metrics')]
final readonly class Metrics implements Model
{
    /**
     * @param bool|null      $enableAiMetrics             A boolean value that determines if AI metrics should be collected. Default: `false`.
     * @param bool|null      $enableBandwidthMetrics      A boolean value that determines if bandwidth metrics should be collected. Default: `false`.
     * @param bool|null      $enableConsumerAttribute     A boolean value that determines if `http.server.request.count`, `http.server.request.size` and `http.server.r… Default: `false`.
     * @param bool|null      $enableLatencyMetrics        A boolean value that determines if latency metrics should be collected. Default: `false`.
     * @param bool|null      $enablePrincipalAttribute    A boolean value that determines if `http.server.request.count`, `http.server.request.size` and `http.server.r… Default: `false`.
     * @param bool|null      $enableRequestMetrics        A boolean value that determines if request count metrics should be collected. Default: `false`.
     * @param bool|null      $enableUpstreamHealthMetrics A boolean value that determines if upstream health metrics should be collected. Default: `false`.
     * @param string|null    $endpoint                    An HTTP URL endpoint where metrics are exported.
     * @param int|float|null $pushInterval                The interval in seconds at which metrics are pushed to the OTLP server. Default: `60`.
     */
    public function __construct(
        public ?bool $enableAiMetrics = null,
        public ?bool $enableBandwidthMetrics = null,
        public ?bool $enableConsumerAttribute = null,
        public ?bool $enableLatencyMetrics = null,
        public ?bool $enablePrincipalAttribute = null,
        public ?bool $enableRequestMetrics = null,
        public ?bool $enableUpstreamHealthMetrics = null,
        public ?string $endpoint = null,
        public int|float|null $pushInterval = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            enableAiMetrics: Data::boolOrNull($data, 'enable_ai_metrics'),
            enableBandwidthMetrics: Data::boolOrNull($data, 'enable_bandwidth_metrics'),
            enableConsumerAttribute: Data::boolOrNull($data, 'enable_consumer_attribute'),
            enableLatencyMetrics: Data::boolOrNull($data, 'enable_latency_metrics'),
            enablePrincipalAttribute: Data::boolOrNull($data, 'enable_principal_attribute'),
            enableRequestMetrics: Data::boolOrNull($data, 'enable_request_metrics'),
            enableUpstreamHealthMetrics: Data::boolOrNull($data, 'enable_upstream_health_metrics'),
            endpoint: Data::stringOrNull($data, 'endpoint'),
            pushInterval: Data::numberOrNull($data, 'push_interval'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'enable_ai_metrics' => $this->enableAiMetrics,
            'enable_bandwidth_metrics' => $this->enableBandwidthMetrics,
            'enable_consumer_attribute' => $this->enableConsumerAttribute,
            'enable_latency_metrics' => $this->enableLatencyMetrics,
            'enable_principal_attribute' => $this->enablePrincipalAttribute,
            'enable_request_metrics' => $this->enableRequestMetrics,
            'enable_upstream_health_metrics' => $this->enableUpstreamHealthMetrics,
            'endpoint' => $this->endpoint,
            'push_interval' => $this->pushInterval,
        ]);
    }
}
