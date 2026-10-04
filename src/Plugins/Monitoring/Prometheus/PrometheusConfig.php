<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Prometheus;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `prometheus` plugin (Prometheus; doc `Monitoring/prometheus.md`).
 *
 * Read it from a returned Plugin with `PrometheusConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('Monitoring/prometheus.md', '#/properties/config')]
final readonly class PrometheusConfig implements PluginConfig
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
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            aiMetrics: Data::boolOrNull($data, 'ai_metrics'),
            bandwidthMetrics: Data::boolOrNull($data, 'bandwidth_metrics'),
            latencyMetrics: Data::boolOrNull($data, 'latency_metrics'),
            perConsumer: Data::boolOrNull($data, 'per_consumer'),
            statusCodeMetrics: Data::boolOrNull($data, 'status_code_metrics'),
            upstreamHealthMetrics: Data::boolOrNull($data, 'upstream_health_metrics'),
            wasmMetrics: Data::boolOrNull($data, 'wasm_metrics'),
        );
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

    /**
     * Reads the typed configuration of a `prometheus` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `prometheus` plugin
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
