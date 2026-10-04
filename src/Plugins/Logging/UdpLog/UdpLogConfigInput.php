<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Logging\UdpLog;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `udp-log` plugin (UDP Log; doc `Logging/udp-log.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('Logging/udp-log.md', '#/properties/config')]
final readonly class UdpLogConfigInput implements Input
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'udp-log';

    /**
     * @param string|null                   $host              A string representing a host name, such as example.com. Required by the plugin doc.
     * @param int|null                      $port              An integer representing a port number between 0 and 65535, inclusive. Required by the plugin doc.
     * @param array<array-key, string>|null $customFieldsByLua Lua code as a key-value map
     * @param int|float|null                $timeout           An optional timeout in milliseconds when sending data to the upstream server. Default: `10000`.
     */
    public function __construct(
        public ?string $host = null,
        public ?int $port = null,
        public ?array $customFieldsByLua = null,
        public int|float|null $timeout = null,
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
            'timeout' => $this->timeout,
        ]);
    }
}
