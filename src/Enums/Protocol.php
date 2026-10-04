<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Enums;

/**
 * Protocols accepted by Services, Routes and Plugins.
 *
 * Spec: `Service.protocol`, `Plugin.protocols[]`, `RouteJson.protocols[]`, `RouteExpression.protocols[]`.
 */
enum Protocol: string
{
    case Grpc = 'grpc';
    case Grpcs = 'grpcs';
    case Http = 'http';
    case Https = 'https';
    case Tcp = 'tcp';
    case Tls = 'tls';
    case TlsPassthrough = 'tls_passthrough';
    case Udp = 'udp';
    case Ws = 'ws';
    case Wss = 'wss';
}
