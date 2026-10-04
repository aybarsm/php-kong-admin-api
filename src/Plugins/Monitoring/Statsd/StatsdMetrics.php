<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Statsd;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.metrics[]` object of the StatsD plugin (doc `Monitoring/statsd.md`).
 */
#[PluginSchema('Monitoring/statsd.md', '#/properties/config/properties/metrics/items')]
final readonly class StatsdMetrics implements Model
{
    /**
     * @param StatsdMetricsName                     $name                StatsD metric’s name.
     * @param StatsdMetricsStatType                 $statType            Determines what sort of event a metric represents.
     * @param StatsdMetricsConsumerIdentifier|null  $consumerIdentifier  Authenticated user detail.
     * @param float|null                            $sampleRate          Sampling rate
     * @param StatsdMetricsServiceIdentifier|null   $serviceIdentifier   Service detail.
     * @param StatsdMetricsWorkspaceIdentifier|null $workspaceIdentifier Workspace detail.
     */
    public function __construct(
        public StatsdMetricsName $name,
        public StatsdMetricsStatType $statType,
        public ?StatsdMetricsConsumerIdentifier $consumerIdentifier = null,
        public ?float $sampleRate = null,
        public ?StatsdMetricsServiceIdentifier $serviceIdentifier = null,
        public ?StatsdMetricsWorkspaceIdentifier $workspaceIdentifier = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            name: Data::enum($data, 'name', StatsdMetricsName::class),
            statType: Data::enum($data, 'stat_type', StatsdMetricsStatType::class),
            consumerIdentifier: Data::enumOrNull($data, 'consumer_identifier', StatsdMetricsConsumerIdentifier::class),
            sampleRate: Data::floatOrNull($data, 'sample_rate'),
            serviceIdentifier: Data::enumOrNull($data, 'service_identifier', StatsdMetricsServiceIdentifier::class),
            workspaceIdentifier: Data::enumOrNull($data, 'workspace_identifier', StatsdMetricsWorkspaceIdentifier::class),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'name' => $this->name->value,
            'stat_type' => $this->statType->value,
            'consumer_identifier' => $this->consumerIdentifier?->value,
            'sample_rate' => $this->sampleRate,
            'service_identifier' => $this->serviceIdentifier?->value,
            'workspace_identifier' => $this->workspaceIdentifier?->value,
        ]);
    }
}
