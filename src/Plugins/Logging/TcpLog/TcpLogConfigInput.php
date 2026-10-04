<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Logging\TcpLog;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `tcp-log` plugin (TCP Log; doc `Logging/tcp-log.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('Logging/tcp-log.md', '#/properties/config')]
final readonly class TcpLogConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'tcp-log';

    /**
     * @param string|null                   $host              The IP address or host name to send data to. Required by the plugin doc.
     * @param int|null                      $port              The port to send data to on the upstream server. Required by the plugin doc.
     * @param array<array-key, string>|null $customFieldsByLua A list of key-value pairs, where the key is the name of a log field and the value is a chunk of Lua code, who…
     * @param int|float|null                $keepalive         An optional value in milliseconds that defines how long an idle connection lives before being closed. Default: `60000`.
     * @param bool|null                     $sslVerify         When using TLS, this option enables verification of the certificate presented by the server. Default: `true`.
     * @param int|float|null                $timeout           An optional timeout in milliseconds when sending data to the upstream server. Default: `10000`.
     * @param bool|null                     $tls               Indicates whether to perform a TLS handshake against the remote server. Default: `false`.
     * @param string|null                   $tlsSni            An optional string that defines the SNI (Server Name Indication) hostname to send in the TLS handshake.
     */
    public function __construct(
        public ?string $host = null,
        public ?int $port = null,
        public ?array $customFieldsByLua = null,
        public int|float|null $keepalive = null,
        public ?bool $sslVerify = null,
        public int|float|null $timeout = null,
        public ?bool $tls = null,
        public ?string $tlsSni = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'host' => $this->host,
            'port' => $this->port,
            'custom_fields_by_lua' => $this->customFieldsByLua,
            'keepalive' => $this->keepalive,
            'ssl_verify' => $this->sslVerify,
            'timeout' => $this->timeout,
            'tls' => $this->tls,
            'tls_sni' => $this->tlsSni,
        ]);
    }
}
