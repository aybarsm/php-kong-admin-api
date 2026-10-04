<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Logging\Syslog;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.successful_severity` in the Syslog Plugin doc.
 */
#[PluginSchema('Logging/syslog.md', '#/properties/config/properties/successful_severity')]
enum SuccessfulSeverity: string
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
