<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Logging\UdpLog;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `udp-log` plugin (UDP Log; doc `Logging/udp-log.md`).
 *
 * Read it from a returned Plugin with `UdpLogConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('Logging/udp-log.md', '#/properties/config')]
final readonly class UdpLogConfig implements PluginConfig
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'udp-log';

    /**
     * @param string                        $host              A string representing a host name, such as example.com.
     * @param int                           $port              An integer representing a port number between 0 and 65535, inclusive.
     * @param array<array-key, string>|null $customFieldsByLua Lua code as a key-value map
     * @param float|null                    $timeout           An optional timeout in milliseconds when sending data to the upstream server. Default: `10000`.
     */
    public function __construct(
        public string $host,
        public int $port,
        public ?array $customFieldsByLua = null,
        public ?float $timeout = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            host: Data::string($data, 'host'),
            port: Data::int($data, 'port'),
            customFieldsByLua: Data::stringMapOrNull($data, 'custom_fields_by_lua'),
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
            'host' => $this->host,
            'port' => $this->port,
            'custom_fields_by_lua' => $this->customFieldsByLua,
            'timeout' => $this->timeout,
        ]);
    }

    /**
     * Reads the typed configuration of a `udp-log` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `udp-log` plugin
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
