<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Logging\Loggly;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;
use SensitiveParameter;

/**
 * Typed `config` request body of a `loggly` plugin (Loggly; doc `Logging/loggly.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('Logging/loggly.md', '#/properties/config')]
final readonly class LogglyConfigInput implements Input
{
    /** Properties the plugin doc marks `x-encrypted`; redacted in __debugInfo(). */
    private const array ENCRYPTED = ['key'];

    /** The plugin's wire name (the doc's front-matter `url`). */
    public const string NAME = 'loggly';

    /**
     * @param string|null                     $key                  This field is [referenceable](/gateway/entities/vault/#how-do-i-reference-secrets-stored-in-a-vault). Required by the plugin doc.
     * @param LogglyClientErrorsSeverity|null $clientErrorsSeverity Default: `info`.
     * @param array<array-key, string>|null   $customFieldsByLua    Lua code as a key-value map
     * @param string|null                     $host                 A string representing a host name, such as example.com. Default: `logs-01.loggly.com`.
     * @param LogglyLogLevel|null             $logLevel             Default: `info`.
     * @param int|null                        $port                 An integer representing a port number between 0 and 65535, inclusive. Default: `514`.
     * @param LogglyServerErrorsSeverity|null $serverErrorsSeverity Default: `info`.
     * @param LogglySuccessfulSeverity|null   $successfulSeverity   Default: `info`.
     * @param list<string>|null               $tags                 Default: `["kong"]`.
     * @param float|null                      $timeout              Default: `10000`.
     */
    public function __construct(
        #[SensitiveParameter]
        public ?string $key = null,
        public ?LogglyClientErrorsSeverity $clientErrorsSeverity = null,
        public ?array $customFieldsByLua = null,
        public ?string $host = null,
        public ?LogglyLogLevel $logLevel = null,
        public ?int $port = null,
        public ?LogglyServerErrorsSeverity $serverErrorsSeverity = null,
        public ?LogglySuccessfulSeverity $successfulSeverity = null,
        public ?array $tags = null,
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
}
