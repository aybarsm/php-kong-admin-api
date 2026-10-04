<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Datadog;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `datadog` plugin (Datadog; doc `Monitoring/datadog.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('Monitoring/datadog.md', '#/properties/config')]
final readonly class DatadogConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'datadog';

    /**
     * @param string|null               $consumerTag    String to be attached as tag of the consumer. Default: `consumer`.
     * @param float|null                $flushTimeout   Optional time in seconds.
     * @param string|null               $host           A string representing a host name, such as example.com. Default: `localhost`.
     * @param list<DatadogMetrics>|null $metrics        List of metrics to be logged.
     * @param int|null                  $port           An integer representing a port number between 0 and 65535, inclusive. Default: `8125`.
     * @param string|null               $prefix         String to be attached as a prefix to a metric's name. Default: `kong`.
     * @param DatadogQueue|null         $queue
     * @param int|null                  $queueSize      Maximum number of log entries to be sent on each message to the upstream server.
     * @param int|null                  $retryCount     Number of times to retry when sending data to the upstream server.
     * @param string|null               $routeNameTag   String to be attached as tag of the route name or ID.
     * @param string|null               $serviceNameTag String to be attached as the name of the service. Default: `name`.
     * @param string|null               $statusTag      String to be attached as the tag of the HTTP status. Default: `status`.
     */
    public function __construct(
        public ?string $consumerTag = null,
        public ?float $flushTimeout = null,
        public ?string $host = null,
        public ?array $metrics = null,
        public ?int $port = null,
        public ?string $prefix = null,
        public ?DatadogQueue $queue = null,
        public ?int $queueSize = null,
        public ?int $retryCount = null,
        public ?string $routeNameTag = null,
        public ?string $serviceNameTag = null,
        public ?string $statusTag = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'consumer_tag' => $this->consumerTag,
            'flush_timeout' => $this->flushTimeout,
            'host' => $this->host,
            'metrics' => Data::toArrays($this->metrics),
            'port' => $this->port,
            'prefix' => $this->prefix,
            'queue' => $this->queue?->toArray(),
            'queue_size' => $this->queueSize,
            'retry_count' => $this->retryCount,
            'route_name_tag' => $this->routeNameTag,
            'service_name_tag' => $this->serviceNameTag,
            'status_tag' => $this->statusTag,
        ]);
    }
}
