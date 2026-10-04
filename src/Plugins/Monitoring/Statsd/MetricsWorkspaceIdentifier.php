<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Statsd;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.metrics[].workspace_identifier` in the StatsD Plugin doc.
 *
 * Workspace detail.
 */
#[PluginSchema('Monitoring/statsd.md', '#/properties/config/properties/metrics/items/properties/workspace_identifier')]
enum MetricsWorkspaceIdentifier: string
{
    case WorkspaceId = 'workspace_id';
    case WorkspaceName = 'workspace_name';
}
