<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Logging\Syslog;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Override;

/**
 * Typed `config` of a `syslog` plugin (Syslog; doc `Logging/syslog.md`).
 *
 * Read it from a returned Plugin with `SyslogConfig::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.
 */
#[PluginSchema('Logging/syslog.md', '#/properties/config')]
final readonly class SyslogConfig implements PluginConfig
{
    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'syslog';

    /**
     * @param ClientErrorsSeverity|null     $clientErrorsSeverity Default: `info`.
     * @param array<array-key, string>|null $customFieldsByLua    Lua code as a key-value map
     * @param Facility|null                 $facility             The facility is used by the operating system to decide how to handle each log message. Default: `user`.
     * @param LogLevel|null                 $logLevel             Default: `info`.
     * @param ServerErrorsSeverity|null     $serverErrorsSeverity Default: `info`.
     * @param SuccessfulSeverity|null       $successfulSeverity   Default: `info`.
     */
    public function __construct(
        public ?ClientErrorsSeverity $clientErrorsSeverity = null,
        public ?array $customFieldsByLua = null,
        public ?Facility $facility = null,
        public ?LogLevel $logLevel = null,
        public ?ServerErrorsSeverity $serverErrorsSeverity = null,
        public ?SuccessfulSeverity $successfulSeverity = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            clientErrorsSeverity: Data::enumOrNull($data, 'client_errors_severity', ClientErrorsSeverity::class),
            customFieldsByLua: Data::stringMapOrNull($data, 'custom_fields_by_lua'),
            facility: Data::enumOrNull($data, 'facility', Facility::class),
            logLevel: Data::enumOrNull($data, 'log_level', LogLevel::class),
            serverErrorsSeverity: Data::enumOrNull($data, 'server_errors_severity', ServerErrorsSeverity::class),
            successfulSeverity: Data::enumOrNull($data, 'successful_severity', SuccessfulSeverity::class),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'client_errors_severity' => $this->clientErrorsSeverity?->value,
            'custom_fields_by_lua' => $this->customFieldsByLua,
            'facility' => $this->facility?->value,
            'log_level' => $this->logLevel?->value,
            'server_errors_severity' => $this->serverErrorsSeverity?->value,
            'successful_severity' => $this->successfulSeverity?->value,
        ]);
    }

    /**
     * Reads the typed configuration of a `syslog` Plugin returned by the Admin API.
     *
     * @throws InvalidArgumentException     when $plugin is not a `syslog` plugin
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
