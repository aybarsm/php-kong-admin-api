<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Statsd;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `statsd` plugin (StatsD; doc `Monitoring/statsd.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('Monitoring/statsd.md', '#/properties/config')]
final readonly class StatsdConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'statsd';

    /**
     * @param list<string>|null               $allowStatusCodes           List of status code ranges that are allowed to be logged in metrics.
     * @param ConsumerIdentifierDefault|null  $consumerIdentifierDefault  Default: `custom_id`.
     * @param int|float|null                  $flushTimeout
     * @param string|null                     $host                       The IP address or hostname of StatsD server to send data to. Default: `localhost`.
     * @param bool|null                       $hostnameInPrefix           Default: `false`.
     * @param list<Metrics>|null              $metrics                    List of metrics to be logged.
     * @param int|null                        $port                       The port of StatsD server to send data to. Default: `8125`.
     * @param string|null                     $prefix                     String to prefix to each metric's name. Default: `kong`.
     * @param Queue|null                      $queue
     * @param int|null                        $queueSize
     * @param int|null                        $retryCount
     * @param ServiceIdentifierDefault|null   $serviceIdentifierDefault   Default: `service_name_or_host`.
     * @param TagStyle|null                   $tagStyle
     * @param int|float|null                  $udpPacketSize              Default: `0`.
     * @param bool|null                       $useTcp                     Default: `false`.
     * @param WorkspaceIdentifierDefault|null $workspaceIdentifierDefault Default: `workspace_id`.
     */
    public function __construct(
        public ?array $allowStatusCodes = null,
        public ?ConsumerIdentifierDefault $consumerIdentifierDefault = null,
        public int|float|null $flushTimeout = null,
        public ?string $host = null,
        public ?bool $hostnameInPrefix = null,
        public ?array $metrics = null,
        public ?int $port = null,
        public ?string $prefix = null,
        public ?Queue $queue = null,
        public ?int $queueSize = null,
        public ?int $retryCount = null,
        public ?ServiceIdentifierDefault $serviceIdentifierDefault = null,
        public ?TagStyle $tagStyle = null,
        public int|float|null $udpPacketSize = null,
        public ?bool $useTcp = null,
        public ?WorkspaceIdentifierDefault $workspaceIdentifierDefault = null,
    ) {
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
}
