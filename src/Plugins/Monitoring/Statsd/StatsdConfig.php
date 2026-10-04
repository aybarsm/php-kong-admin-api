<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Statsd;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `statsd` plugin (StatsD; doc `Monitoring/statsd.md`).
 *
 * Read it from a returned Plugin with `StatsdConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('Monitoring/statsd.md', '#/properties/config')]
final readonly class StatsdConfig implements PluginConfig
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'statsd';

    /**
     * @param list<string>|null                     $allowStatusCodes           List of status code ranges that are allowed to be logged in metrics.
     * @param StatsdConsumerIdentifierDefault|null  $consumerIdentifierDefault  Default: `custom_id`.
     * @param float|null                            $flushTimeout
     * @param string|null                           $host                       The IP address or hostname of StatsD server to send data to. Default: `localhost`.
     * @param bool|null                             $hostnameInPrefix           Default: `false`.
     * @param list<StatsdMetrics>|null              $metrics                    List of metrics to be logged.
     * @param int|null                              $port                       The port of StatsD server to send data to. Default: `8125`.
     * @param string|null                           $prefix                     String to prefix to each metric's name. Default: `kong`.
     * @param StatsdQueue|null                      $queue
     * @param int|null                              $queueSize
     * @param int|null                              $retryCount
     * @param StatsdServiceIdentifierDefault|null   $serviceIdentifierDefault   Default: `service_name_or_host`.
     * @param StatsdTagStyle|null                   $tagStyle
     * @param float|null                            $udpPacketSize              Default: `0`.
     * @param bool|null                             $useTcp                     Default: `false`.
     * @param StatsdWorkspaceIdentifierDefault|null $workspaceIdentifierDefault Default: `workspace_id`.
     */
    public function __construct(
        public ?array $allowStatusCodes = null,
        public ?StatsdConsumerIdentifierDefault $consumerIdentifierDefault = null,
        public ?float $flushTimeout = null,
        public ?string $host = null,
        public ?bool $hostnameInPrefix = null,
        public ?array $metrics = null,
        public ?int $port = null,
        public ?string $prefix = null,
        public ?StatsdQueue $queue = null,
        public ?int $queueSize = null,
        public ?int $retryCount = null,
        public ?StatsdServiceIdentifierDefault $serviceIdentifierDefault = null,
        public ?StatsdTagStyle $tagStyle = null,
        public ?float $udpPacketSize = null,
        public ?bool $useTcp = null,
        public ?StatsdWorkspaceIdentifierDefault $workspaceIdentifierDefault = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $metrics = Data::listOfMapsOrNull($data, 'metrics');
        $queue = Data::mapOrNull($data, 'queue');

        return new self(
            allowStatusCodes: Data::stringListOrNull($data, 'allow_status_codes'),
            consumerIdentifierDefault: Data::enumOrNull($data, 'consumer_identifier_default', StatsdConsumerIdentifierDefault::class),
            flushTimeout: Data::floatOrNull($data, 'flush_timeout'),
            host: Data::stringOrNull($data, 'host'),
            hostnameInPrefix: Data::boolOrNull($data, 'hostname_in_prefix'),
            metrics: $metrics === null ? null : array_map(StatsdMetrics::fromArray(...), $metrics),
            port: Data::intOrNull($data, 'port'),
            prefix: Data::stringOrNull($data, 'prefix'),
            queue: $queue === null ? null : StatsdQueue::fromArray($queue),
            queueSize: Data::intOrNull($data, 'queue_size'),
            retryCount: Data::intOrNull($data, 'retry_count'),
            serviceIdentifierDefault: Data::enumOrNull($data, 'service_identifier_default', StatsdServiceIdentifierDefault::class),
            tagStyle: Data::enumOrNull($data, 'tag_style', StatsdTagStyle::class),
            udpPacketSize: Data::floatOrNull($data, 'udp_packet_size'),
            useTcp: Data::boolOrNull($data, 'use_tcp'),
            workspaceIdentifierDefault: Data::enumOrNull($data, 'workspace_identifier_default', StatsdWorkspaceIdentifierDefault::class),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'allow_status_codes' => $this->allowStatusCodes,
            'consumer_identifier_default' => $this->consumerIdentifierDefault?->value,
            'flush_timeout' => $this->flushTimeout,
            'host' => $this->host,
            'hostname_in_prefix' => $this->hostnameInPrefix,
            'metrics' => Data::toArrays($this->metrics),
            'port' => $this->port,
            'prefix' => $this->prefix,
            'queue' => $this->queue?->toArray(),
            'queue_size' => $this->queueSize,
            'retry_count' => $this->retryCount,
            'service_identifier_default' => $this->serviceIdentifierDefault?->value,
            'tag_style' => $this->tagStyle?->value,
            'udp_packet_size' => $this->udpPacketSize,
            'use_tcp' => $this->useTcp,
            'workspace_identifier_default' => $this->workspaceIdentifierDefault?->value,
        ]);
    }

    /**
     * Reads the typed configuration of a `statsd` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `statsd` plugin
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
