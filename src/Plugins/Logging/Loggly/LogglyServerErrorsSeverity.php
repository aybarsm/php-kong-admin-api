<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Logging\Loggly;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.server_errors_severity` in the Loggly Plugin doc.
 */
#[PluginSchema('Logging/loggly.md', '#/properties/config/properties/server_errors_severity')]
enum LogglyServerErrorsSeverity: string
{
    case Alert = 'alert';
    case Crit = 'crit';
    case Debug = 'debug';
    case Emerg = 'emerg';
    case Err = 'err';
    case Info = 'info';
    case Notice = 'notice';
    case Warning = 'warning';
}
