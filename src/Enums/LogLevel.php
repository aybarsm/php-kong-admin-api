<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Enums;

/**
 * Kong log levels for the debug endpoints.
 *
 * Spec: `paths./debug/node/log-level/{logLevel}.parameters.0.schema`, `paths./debug/cluster/log-level/{logLevel}.parameters.0.schema`, `paths./debug/cluster/control-planes-nodes/log-level/{logLevel}.parameters.0.schema`.
 */
enum LogLevel: string
{
    case Debug = 'debug';
    case Info = 'info';
    case Notice = 'notice';
    case Warn = 'warn';
    case Error = 'error';
    case Crit = 'crit';
}
