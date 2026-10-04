<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Datadog;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `datadog` plugin (Datadog; doc `Monitoring/datadog.md`).
 *
 * Read it from a returned Plugin with `DatadogConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('Monitoring/datadog.md', '#/properties/config')]
final readonly class DatadogConfig implements PluginConfig
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'datadog';

    /**
     * @param string|null        $consumerTag    String to be attached as tag of the consumer. Default: `consumer`.
     * @param int|float|null     $flushTimeout   Optional time in seconds.
     * @param string|null        $host           A string representing a host name, such as example.com. Default: `localhost`.
     * @param list<Metrics>|null $metrics        List of metrics to be logged.
     * @param int|null           $port           An integer representing a port number between 0 and 65535, inclusive. Default: `8125`.
     * @param string|null        $prefix         String to be attached as a prefix to a metric's name. Default: `kong`.
     * @param Queue|null         $queue
     * @param int|null           $queueSize      Maximum number of log entries to be sent on each message to the upstream server.
     * @param int|null           $retryCount     Number of times to retry when sending data to the upstream server.
     * @param string|null        $routeNameTag   String to be attached as tag of the route name or ID.
     * @param string|null        $serviceNameTag String to be attached as the name of the service. Default: `name`.
     * @param string|null        $statusTag      String to be attached as the tag of the HTTP status. Default: `status`.
     */
    public function __construct(
        public ?string $consumerTag = null,
        public int|float|null $flushTimeout = null,
        public ?string $host = null,
        public ?array $metrics = null,
        public ?int $port = null,
        public ?string $prefix = null,
        public ?Queue $queue = null,
        public ?int $queueSize = null,
        public ?int $retryCount = null,
        public ?string $routeNameTag = null,
        public ?string $serviceNameTag = null,
        public ?string $statusTag = null,
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
            consumerTag: Data::stringOrNull($data, 'consumer_tag'),
            flushTimeout: Data::numberOrNull($data, 'flush_timeout'),
            host: Data::stringOrNull($data, 'host'),
            metrics: $metrics === null ? null : array_map(Metrics::fromArray(...), $metrics),
            port: Data::intOrNull($data, 'port'),
            prefix: Data::stringOrNull($data, 'prefix'),
            queue: $queue === null ? null : Queue::fromArray($queue),
            queueSize: Data::intOrNull($data, 'queue_size'),
            retryCount: Data::intOrNull($data, 'retry_count'),
            routeNameTag: Data::stringOrNull($data, 'route_name_tag'),
            serviceNameTag: Data::stringOrNull($data, 'service_name_tag'),
            statusTag: Data::stringOrNull($data, 'status_tag'),
        );
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

    /**
     * Reads the typed configuration of a `datadog` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `datadog` plugin
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
