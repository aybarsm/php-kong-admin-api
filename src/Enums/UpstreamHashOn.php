<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Enums;

/**
 * Hashing inputs of an Upstream (`hash_on` and `hash_fallback` share this set).
 *
 * Spec: `components.schemas.Upstream.hash_on`, `components.schemas.Upstream.hash_fallback`.
 */
enum UpstreamHashOn: string
{
    case Consumer = 'consumer';
    case Cookie = 'cookie';
    case Header = 'header';
    case Ip = 'ip';
    case None = 'none';
    case Path = 'path';
    case QueryArg = 'query_arg';
    case UriCapture = 'uri_capture';
}
