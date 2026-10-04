"""Phase 4d: operational endpoints (spec tags Information, Debug, Clustering, Config, Cache, Keyring, Audit Logs,
Schemas, and `GET /schemas/plugins/{pluginName}`).

None of these operations has the CRUD shape, so this phase generates only DTOs (and their fixtures). The
resources (Information, Debug, Clustering, DeclarativeConfig, Cache, Keyring, AuditLogs, Schemas) and their
tests are hand-written.

Run from the repo root:  python3 tools/generator/phase4d.py
"""
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent))

import models  # noqa: E402
from feature_tests import snake  # noqa: E402
from fixtures import fixture  # noqa: E402
from models import finalize, generate_entity  # noqa: E402

R = '#/components/responses/'
RB = '#/components/requestBodies/'
JSON = '/content/application~1json/schema'

ENUMS = {}

NESTED = {
    ('KongInfo', 'pids'): 'KongInfoPids',
    ('KongInfo', 'plugins'): 'KongInfoPlugins',
    ('KongInfo', 'timers'): 'KongInfoTimers',
    ('NodeStatus', 'memory'): 'NodeStatusMemory',
    ('NodeStatus', 'memory.workers_lua_vms[]'): 'NodeStatusWorkerVm',
    ('DnsStatus', 'worker'): 'DnsStatusWorker',
    ('Timers', 'stats'): 'TimersStats',
    ('Timers', 'stats.flamegraph'): 'TimersFlamegraph',
    ('Timers', 'stats.sys'): 'TimersSys',
    ('Timers', 'stats.timers{}'): 'TimersTimer',
    ('Timers', 'stats.timers.meta'): 'TimersTimerMeta',
    ('Timers', 'stats.timers.stats'): 'TimersTimerStats',
    ('Timers', 'stats.timers.stats.elapsed_time'): 'TimersTimerElapsedTime',
    ('Timers', 'worker'): 'TimersWorker',
    ('DataPlane', 'cert_details'): 'DataPlaneCertDetails',
    ('DataPlane', 'labels'): 'DataPlaneLabels',
}

OUTPUTS = [
    ('KongInfo', R + 'GetKongInfoResponse' + JSON, ['Node information from `GET /` (spec response `GetKongInfoResponse`).']),
    ('NodeStatus', R + 'GetNodeStatusResponse' + JSON, ['Node status from `GET /status` (spec response `GetNodeStatusResponse`).']),
    ('DnsStatus', R + 'GetDNSStatusResponse' + JSON, ['DNS status from `GET /status/dns` (spec response `GetDNSStatusResponse`).']),
    ('Timers', R + 'GetTimersDebugInfoResponse' + JSON,
     ['Timer debug information from `GET /timers` (spec response `GetTimersDebugInfoResponse`).']),
    ('FipsStatus', R + 'FIPS-response' + JSON, ['FIPS status from `GET /fips-status` (spec response `FIPS-response`).']),
    ('AuditObject', R + 'DatabaseAuditLogResponse' + JSON,
     ['A database audit record from `GET /audit/objects` (spec response `DatabaseAuditLogResponse`).']),
    ('AuditRequest', R + 'ListAuditObjectsResponse' + JSON,
     ['A request audit record from `GET /audit/requests` (spec response `ListAuditObjectsResponse`).']),
    ('CacheEntry', R + 'CacheEntryFoundResponse' + JSON, ['A cache entry from `GET /cache/{key}` (spec response `CacheEntryFoundResponse`).']),
    ('DataPlane', R + 'GetConnectedDataPlanesListResponse' + JSON + '/properties/data',
     ['A connected data plane from `GET /clustering/data-planes` (spec response `GetConnectedDataPlanesListResponse`).']),
    ('DataPlaneStatus', R + 'GetConnectedDataPlaneStatusResponse' + JSON + '/additionalProperties',
     ['The status of one data plane from `GET /clustering/status` (values of spec response',
      '`GetConnectedDataPlaneStatusResponse`, keyed by node ID).']),
    ('DeclarativeConfig', R + 'GetDeclarativeConfigResponse' + JSON,
     ['The declarative configuration from `GET /config` (spec response `GetDeclarativeConfigResponse`).']),
    ('NodeLogLevel', R + 'GetNodeLogLevelResponse' + JSON,
     ['The node log-level message (spec responses `GetNodeLogLevelResponse` and `UpdateNodeLogLevelResponse`).']),
    ('KeyringStatus', R + 'KeyRingResponse' + JSON, ['The keyring state from `GET /keyring` (spec response `KeyRingResponse`).']),
    ('Keyring', 'Keyring', ['A keyring key (spec schema `Keyring`).']),
    ('KeyringImportResult', R + 'CreateKeyringImportResponse' + JSON,
     ['The response of `POST /keyring/import` (spec response `CreateKeyringImportResponse`).']),
    ('SchemaValidation', R + 'ValidateEntityResponse' + JSON,
     ['The result of `POST /schemas/{entityName}/validate` (spec response `ValidateEntityResponse`).']),
    ('PartialTypeSchema', R + 'GetPartialSchemaResponse' + JSON,
     ['The schema of a Partial type from `GET /schemas/partials/{partialType}` (spec response `GetPartialSchemaResponse`).']),
    ('PluginConfigSchema', R + 'GetPluginSchemaResponse' + JSON,
     ['The schema of a plugin from `GET /schemas/plugins/{pluginName}` (spec response `GetPluginSchemaResponse`).']),
]

INPUTS = [
    ('Keyring', RB + 'KeyringRequest' + JSON,
     ['Request body for `POST /keyring/{activate,generate,remove}` (spec request body `KeyringRequest`).']),
    ('KeyringImport', RB + 'CreateKeyringImportRequest' + JSON,
     ['Request body for `POST /keyring/import` (spec request body `CreateKeyringImportRequest`).']),
    ('KeyringVaultSync', RB + 'UpdateKeyringVaultSyncRequest' + JSON,
     ['Request body for `POST /keyring/vault/sync` (spec request body `UpdateKeyringVaultSyncRequest`).']),
]


def main():
    for cls, schema, doc in OUTPUTS:
        generate_entity(cls, schema, doc, NESTED, ENUMS, with_input=False)
    for cls, schema, doc in INPUTS:
        generate_entity(cls, schema, doc, NESTED, ENUMS, input_doc=doc, with_output=False)
    for cls, schema, _ in OUTPUTS:
        fixture(snake(cls), schema)
    finalize()


if __name__ == '__main__':
    main()
