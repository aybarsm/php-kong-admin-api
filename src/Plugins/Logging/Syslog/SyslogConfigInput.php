<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Logging\Syslog;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Typed `config` request body of a `syslog` plugin (Syslog; doc `Logging/syslog.md`).
 *
 * Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a
 * vault reference to a non-string field, pass `config` as an array instead.
 */
#[PluginSchema('Logging/syslog.md', '#/properties/config')]
final readonly class SyslogConfigInput implements Input
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
}
