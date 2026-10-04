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
final readonly class Metrics implements Model
{
    /**
     * @param MetricsName                     $name                StatsD metric’s name.
     * @param MetricsStatType                 $statType            Determines what sort of event a metric represents.
     * @param MetricsConsumerIdentifier|null  $consumerIdentifier  Authenticated user detail.
     * @param int|float|null                  $sampleRate          Sampling rate
     * @param MetricsServiceIdentifier|null   $serviceIdentifier   Service detail.
     * @param MetricsWorkspaceIdentifier|null $workspaceIdentifier Workspace detail.
     */
    public function __construct(
        public MetricsName $name,
        public MetricsStatType $statType,
        public ?MetricsConsumerIdentifier $consumerIdentifier = null,
        public int|float|null $sampleRate = null,
        public ?MetricsServiceIdentifier $serviceIdentifier = null,
        public ?MetricsWorkspaceIdentifier $workspaceIdentifier = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            name: Data::enum($data, 'name', MetricsName::class),
            statType: Data::enum($data, 'stat_type', MetricsStatType::class),
            consumerIdentifier: Data::enumOrNull($data, 'consumer_identifier', MetricsConsumerIdentifier::class),
            sampleRate: Data::numberOrNull($data, 'sample_rate'),
            serviceIdentifier: Data::enumOrNull($data, 'service_identifier', MetricsServiceIdentifier::class),
            workspaceIdentifier: Data::enumOrNull($data, 'workspace_identifier', MetricsWorkspaceIdentifier::class),
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
