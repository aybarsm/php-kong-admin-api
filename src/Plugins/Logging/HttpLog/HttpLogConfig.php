<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Logging\HttpLog;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `http-log` plugin (HTTP Log; doc `Logging/http-log.md`).
 *
 * Read it from a returned Plugin with `HttpLogConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('Logging/http-log.md', '#/properties/config')]
final readonly class HttpLogConfig implements PluginConfig
{
    /** Properties the plugin doc marks `x-encrypted`; redacted in __debugInfo(). */
    private const array ENCRYPTED = ['httpEndpoint'];

    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'http-log';

    /**
     * @param string                        $httpEndpoint      A string representing a URL, such as https://example.com/path/to/resource?q=search.
     * @param ForeignKey|null               $clientCertificate Certificate to use as the mTLS client certificate when connecting to the configured HTTPS endpoint.
     * @param HttpLogContentType|null       $contentType       Indicates the type of data sent. Default: `application/json`.
     * @param array<array-key, string>|null $customFieldsByLua Lua code as a key-value map
     * @param float|null                    $flushTimeout      Optional time in seconds.
     * @param array<array-key, string>|null $headers           An optional table of headers included in the HTTP message to the upstream server.
     * @param float|null                    $keepalive         An optional value in milliseconds that defines how long an idle connection will live before being closed. Default: `60000`.
     * @param HttpLogMethod|null            $method            An optional method used to send data to the HTTP server. Default: `POST`.
     * @param HttpLogQueue|null             $queue
     * @param int|null                      $queueSize         Maximum number of log entries to be sent on each message to the upstream server.
     * @param int|null                      $retryCount        Number of times to retry when sending data to the upstream server.
     * @param bool|null                     $sslVerify         When using TLS, this option enables verification of the certificate presented by the server. Default: `true`.
     * @param float|null                    $timeout           An optional timeout in milliseconds when sending data to the upstream server. Default: `10000`.
     */
    public function __construct(
        public string $httpEndpoint,
        public ?ForeignKey $clientCertificate = null,
        public ?HttpLogContentType $contentType = null,
        public ?array $customFieldsByLua = null,
        public ?float $flushTimeout = null,
        public ?array $headers = null,
        public ?float $keepalive = null,
        public ?HttpLogMethod $method = null,
        public ?HttpLogQueue $queue = null,
        public ?int $queueSize = null,
        public ?int $retryCount = null,
        public ?bool $sslVerify = null,
        public ?float $timeout = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $clientCertificate = Data::mapOrNull($data, 'client_certificate');
        $queue = Data::mapOrNull($data, 'queue');

        return new self(
            httpEndpoint: Data::string($data, 'http_endpoint'),
            clientCertificate: $clientCertificate === null ? null : ForeignKey::fromArray($clientCertificate),
            contentType: Data::enumOrNull($data, 'content_type', HttpLogContentType::class),
            customFieldsByLua: Data::stringMapOrNull($data, 'custom_fields_by_lua'),
            flushTimeout: Data::floatOrNull($data, 'flush_timeout'),
            headers: Data::stringMapOrNull($data, 'headers'),
            keepalive: Data::floatOrNull($data, 'keepalive'),
            method: Data::enumOrNull($data, 'method', HttpLogMethod::class),
            queue: $queue === null ? null : HttpLogQueue::fromArray($queue),
            queueSize: Data::intOrNull($data, 'queue_size'),
            retryCount: Data::intOrNull($data, 'retry_count'),
            sslVerify: Data::boolOrNull($data, 'ssl_verify'),
            timeout: Data::floatOrNull($data, 'timeout'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'http_endpoint' => $this->httpEndpoint,
            'client_certificate' => $this->clientCertificate?->toArray(),
            'content_type' => $this->contentType?->value,
            'custom_fields_by_lua' => $this->customFieldsByLua,
            'flush_timeout' => $this->flushTimeout,
            'headers' => $this->headers,
            'keepalive' => $this->keepalive,
            'method' => $this->method?->value,
            'queue' => $this->queue?->toArray(),
            'queue_size' => $this->queueSize,
            'retry_count' => $this->retryCount,
            'ssl_verify' => $this->sslVerify,
            'timeout' => $this->timeout,
        ]);
    }

    /**
     * Redacts `x-encrypted` values.
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        $values = get_object_vars($this);
        foreach (self::ENCRYPTED as $property) {
            if ($values[$property] !== null) {
                $values[$property] = '***';
            }
        }

        return $values;
    }

    /**
     * Reads the typed configuration of a `http-log` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `http-log` plugin
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
