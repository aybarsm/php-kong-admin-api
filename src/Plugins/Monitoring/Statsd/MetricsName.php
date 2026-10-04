<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Statsd;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.metrics[].name` in the StatsD Plugin doc.
 *
 * StatsD metric’s name.
 */
#[PluginSchema('Monitoring/statsd.md', '#/properties/config/properties/metrics/items/properties/name')]
enum MetricsName: string
{
    case CacheDatastoreHitsTotal = 'cache_datastore_hits_total';
    case CacheDatastoreMissesTotal = 'cache_datastore_misses_total';
    case KongLatency = 'kong_latency';
    case Latency = 'latency';
    case RequestCount = 'request_count';
    case RequestPerUser = 'request_per_user';
    case RequestSize = 'request_size';
    case ResponseSize = 'response_size';
    case ShdictUsage = 'shdict_usage';
    case StatusCount = 'status_count';
    case StatusCountPerUser = 'status_count_per_user';
    case StatusCountPerUserPerRoute = 'status_count_per_user_per_route';
    case StatusCountPerWorkspace = 'status_count_per_workspace';
    case UniqueUsers = 'unique_users';
    case UpstreamLatency = 'upstream_latency';
}
