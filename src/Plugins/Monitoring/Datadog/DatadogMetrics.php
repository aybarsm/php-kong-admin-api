<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Datadog;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.metrics[]` object of the Datadog plugin (doc `Monitoring/datadog.md`).
 */
#[PluginSchema('Monitoring/datadog.md', '#/properties/config/properties/metrics/items')]
final readonly class DatadogMetrics implements Model
{
    /**
     * @param DatadogMetricsName                    $name               Datadog metric’s name
     * @param DatadogMetricsStatType                $statType           Determines what sort of event the metric represents
     * @param DatadogMetricsConsumerIdentifier|null $consumerIdentifier Authenticated user detail
     * @param float|null                            $sampleRate         Sampling rate
     * @param list<string>|null                     $tags               List of tags
     */
    public function __construct(
        public DatadogMetricsName $name,
        public DatadogMetricsStatType $statType,
        public ?DatadogMetricsConsumerIdentifier $consumerIdentifier = null,
        public ?float $sampleRate = null,
        public ?array $tags = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            name: Data::enum($data, 'name', DatadogMetricsName::class),
            statType: Data::enum($data, 'stat_type', DatadogMetricsStatType::class),
            consumerIdentifier: Data::enumOrNull($data, 'consumer_identifier', DatadogMetricsConsumerIdentifier::class),
            sampleRate: Data::floatOrNull($data, 'sample_rate'),
            tags: Data::stringListOrNull($data, 'tags'),
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
            'tags' => $this->tags,
        ]);
    }
}
