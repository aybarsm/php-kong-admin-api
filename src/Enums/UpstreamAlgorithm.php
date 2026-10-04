<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Enums;

/**
 * Load-balancing algorithms of an Upstream.
 *
 * Spec: `components.schemas.Upstream.algorithm`.
 */
enum UpstreamAlgorithm: string
{
    case ConsistentHashing = 'consistent-hashing';
    case Latency = 'latency';
    case LeastConnections = 'least-connections';
    case RoundRobin = 'round-robin';
    case StickySessions = 'sticky-sessions';
}
