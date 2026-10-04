<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Logging\Loggly;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.log_level` in the Loggly Plugin doc.
 */
#[PluginSchema('Logging/loggly.md', '#/properties/config/properties/log_level')]
enum LogglyLogLevel: string
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
