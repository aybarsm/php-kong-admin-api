<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Logging\Syslog;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.facility` in the Syslog Plugin doc.
 *
 * The facility is used by the operating system to decide how to handle each log message.
 */
#[PluginSchema('Logging/syslog.md', '#/properties/config/properties/facility')]
enum Facility: string
{
    case Auth = 'auth';
    case Authpriv = 'authpriv';
    case Cron = 'cron';
    case Daemon = 'daemon';
    case Ftp = 'ftp';
    case Kern = 'kern';
    case Local0 = 'local0';
    case Local1 = 'local1';
    case Local2 = 'local2';
    case Local3 = 'local3';
    case Local4 = 'local4';
    case Local5 = 'local5';
    case Local6 = 'local6';
    case Local7 = 'local7';
    case Lpr = 'lpr';
    case Mail = 'mail';
    case News = 'news';
    case Syslog = 'syslog';
    case User = 'user';
    case Uucp = 'uucp';
}
