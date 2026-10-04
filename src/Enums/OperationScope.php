<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Enums;

/**
 * Where an operation exists in the spec relative to the `/{workspace}` path prefix.
 */
enum OperationScope: string
{
    /** The spec defines the path both unprefixed and as a `/{workspace}` twin. */
    case Both = 'both';

    /** The spec defines the path only without a `/{workspace}` prefix. */
    case GlobalOnly = 'global';

    /** The spec defines the path only under the `/{workspace}` prefix. */
    case WorkspaceOnly = 'workspace';
}
