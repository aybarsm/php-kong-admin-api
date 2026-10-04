<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Statsd;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.workspace_identifier_default` in the StatsD Plugin doc.
 */
#[PluginSchema('Monitoring/statsd.md', '#/properties/config/properties/workspace_identifier_default')]
enum StatsdWorkspaceIdentifierDefault: string
{
    case WorkspaceId = 'workspace_id';
    case WorkspaceName = 'workspace_name';
}
