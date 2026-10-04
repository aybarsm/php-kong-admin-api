<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Prometheus;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `prometheus` plugin (Prometheus; doc `Monitoring/prometheus.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('Monitoring/prometheus.md', '#/properties/config')]
final readonly class PrometheusConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'prometheus';

    /**
     * @param bool|null $aiMetrics             A boolean value that determines if ai metrics should be collected. Default: `false`.
     * @param bool|null $bandwidthMetrics      A boolean value that determines if bandwidth metrics should be collected. Default: `false`.
     * @param bool|null $latencyMetrics        A boolean value that determines if latency metrics should be collected. Default: `false`.
     * @param bool|null $perConsumer           A boolean value that determines if per-consumer metrics should be collected. Default: `false`.
     * @param bool|null $statusCodeMetrics     A boolean value that determines if status code metrics should be collected. Default: `false`.
     * @param bool|null $upstreamHealthMetrics A boolean value that determines if upstream metrics should be collected. Default: `false`.
     * @param bool|null $wasmMetrics
     */
    public function __construct(
        public ?bool $aiMetrics = null,
        public ?bool $bandwidthMetrics = null,
        public ?bool $latencyMetrics = null,
        public ?bool $perConsumer = null,
        public ?bool $statusCodeMetrics = null,
        public ?bool $upstreamHealthMetrics = null,
        public ?bool $wasmMetrics = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'ai_metrics' => $this->aiMetrics,
            'bandwidth_metrics' => $this->bandwidthMetrics,
            'latency_metrics' => $this->latencyMetrics,
            'per_consumer' => $this->perConsumer,
            'status_code_metrics' => $this->statusCodeMetrics,
            'upstream_health_metrics' => $this->upstreamHealthMetrics,
            'wasm_metrics' => $this->wasmMetrics,
        ]);
    }
}
