<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Logging\Loggly;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `loggly` plugin (Loggly; doc `Logging/loggly.md`).
 *
 * Read it from a returned Plugin with `LogglyConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('Logging/loggly.md', '#/properties/config')]
final readonly class LogglyConfig implements PluginConfig
{
    /** Properties the plugin doc marks `x-encrypted`; redacted in __debugInfo(). */
    private const array ENCRYPTED = ['key'];

    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'loggly';

    /**
     * @param string                        $key                  This field is [referenceable](/gateway/entities/vault/#how-do-i-reference-secrets-stored-in-a-vault).
     * @param ClientErrorsSeverity|null     $clientErrorsSeverity Default: `info`.
     * @param array<array-key, string>|null $customFieldsByLua    Lua code as a key-value map
     * @param string|null                   $host                 A string representing a host name, such as example.com. Default: `logs-01.loggly.com`.
     * @param LogLevel|null                 $logLevel             Default: `info`.
     * @param int|null                      $port                 An integer representing a port number between 0 and 65535, inclusive. Default: `514`.
     * @param ServerErrorsSeverity|null     $serverErrorsSeverity Default: `info`.
     * @param SuccessfulSeverity|null       $successfulSeverity   Default: `info`.
     * @param list<string>|null             $tags                 Default: `["kong"]`.
     * @param int|float|null                $timeout              Default: `10000`.
     */
    public function __construct(
        public string $key,
        public ?ClientErrorsSeverity $clientErrorsSeverity = null,
        public ?array $customFieldsByLua = null,
        public ?string $host = null,
        public ?LogLevel $logLevel = null,
        public ?int $port = null,
        public ?ServerErrorsSeverity $serverErrorsSeverity = null,
        public ?SuccessfulSeverity $successfulSeverity = null,
        public ?array $tags = null,
        public int|float|null $timeout = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            key: Data::string($data, 'key'),
            clientErrorsSeverity: Data::enumOrNull($data, 'client_errors_severity', ClientErrorsSeverity::class),
            customFieldsByLua: Data::stringMapOrNull($data, 'custom_fields_by_lua'),
            host: Data::stringOrNull($data, 'host'),
            logLevel: Data::enumOrNull($data, 'log_level', LogLevel::class),
            port: Data::intOrNull($data, 'port'),
            serverErrorsSeverity: Data::enumOrNull($data, 'server_errors_severity', ServerErrorsSeverity::class),
            successfulSeverity: Data::enumOrNull($data, 'successful_severity', SuccessfulSeverity::class),
            tags: Data::stringListOrNull($data, 'tags'),
            timeout: Data::numberOrNull($data, 'timeout'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'key' => $this->key,
            'client_errors_severity' => $this->clientErrorsSeverity?->value,
            'custom_fields_by_lua' => $this->customFieldsByLua,
            'host' => $this->host,
            'log_level' => $this->logLevel?->value,
            'port' => $this->port,
            'server_errors_severity' => $this->serverErrorsSeverity?->value,
            'successful_severity' => $this->successfulSeverity?->value,
            'tags' => $this->tags,
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
     * Reads the typed configuration of a `loggly` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `loggly` plugin
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
