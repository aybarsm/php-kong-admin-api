<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Logging\HttpLog;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;
use SensitiveParameter;

/**
 * Typed `config` request body of a `http-log` plugin (HTTP Log; doc `Logging/http-log.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('Logging/http-log.md', '#/properties/config')]
final readonly class HttpLogConfigInput implements Input
{
    /** Properties the plugin doc marks `x-encrypted`; redacted in __debugInfo(). */
    private const array ENCRYPTED = ['httpEndpoint'];

    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'http-log';

    /**
     * @param string|null                   $httpEndpoint      A string representing a URL, such as https://example.com/path/to/resource?q=search. Required by the plugin doc.
     * @param ForeignKey|string|null        $clientCertificate Certificate to use as the mTLS client certificate when connecting to the configured HTTPS endpoint.
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
        #[SensitiveParameter]
        public ?string $httpEndpoint = null,
        public ForeignKey|string|null $clientCertificate = null,
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
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'http_endpoint' => $this->httpEndpoint,
            'client_certificate' => $this->clientCertificate === null ? null : ForeignKey::of($this->clientCertificate)->toArray(),
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
}
