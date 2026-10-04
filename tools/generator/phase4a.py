"""Phase 4a: core gateway entities (spec tags Consumers, Plugins, Upstreams, Targets, Certificates, SNIs,
CA Certificates, Vaults, Keys, KeySets, Workspaces, Tags).

Generates the Models, Resources, fixtures and feature tests for those entities. Services, Routes, Tags
(resource) and the KongClient/Services/Routes accessors are hand-written and not touched here.

Run from the repo root:  python3 tools/generator/phase4a.py
"""
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent))

from feature_tests import feature_test, snake  # noqa: E402
from fixtures import fixture  # noqa: E402
from models import NS, finalize, generate_entity  # noqa: E402
from resources import resource  # noqa: E402

# --- Models ------------------------------------------------------------------------------------
# Enum class per enum location: (owner class, dotted property path, `[]` for array items).
ENUMS = {
    ('Plugin', 'protocols[]'): 'Protocol',
    ('Upstream', 'algorithm'): 'UpstreamAlgorithm',
    ('Upstream', 'hash_on'): 'UpstreamHashOn',
    ('Upstream', 'hash_fallback'): 'UpstreamHashOn',
    ('Upstream', 'healthchecks.active.type'): 'HealthcheckType',
    ('Upstream', 'healthchecks.passive.type'): 'HealthcheckType',
}

NESTED = {
    ('Plugin', 'ordering'): 'PluginOrdering',
    ('Plugin', 'ordering.after'): 'PluginOrderingPhases',
    ('Plugin', 'ordering.before'): 'PluginOrderingPhases',
    ('Plugin', 'partials[]'): 'PluginPartial',
    ('Upstream', 'healthchecks'): 'UpstreamHealthchecks',
    ('Upstream', 'healthchecks.active'): 'UpstreamActiveHealthcheck',
    ('Upstream', 'healthchecks.active.healthy'): 'UpstreamActiveHealthy',
    ('Upstream', 'healthchecks.active.unhealthy'): 'UpstreamActiveUnhealthy',
    ('Upstream', 'healthchecks.passive'): 'UpstreamPassiveHealthcheck',
    ('Upstream', 'healthchecks.passive.healthy'): 'UpstreamPassiveHealthy',
    ('Upstream', 'healthchecks.passive.unhealthy'): 'UpstreamPassiveUnhealthy',
    ('Key', 'pem'): 'KeyPem',
    ('Workspace', 'config'): 'WorkspaceConfig',
    ('Workspace', 'meta'): 'WorkspaceMeta',
}

ENTITIES = [
    ('Consumer', 'Consumer', ['A Consumer as returned by the Admin API (spec schema `Consumer`).']),
    ('Plugin', 'Plugin', ['A Plugin as returned by the Admin API (spec schema `Plugin`).', '',
                          'Nested POST/PUT bodies use `PluginWithoutParents`, which has the same properties.']),
    ('Upstream', 'Upstream', ['An Upstream (load-balancing target group) as returned by the Admin API (spec schema `Upstream`).']),
    ('Target', 'Target', ['A Target of an Upstream as returned by the Admin API (spec schema `Target`).', '',
                          'The spec types `created_at`/`updated_at` as `number` here, unlike other entities (spec-notes Q14).']),
    ('Certificate', 'Certificate', ['A Certificate as returned by the Admin API (spec schema `Certificate`).']),
    ('Sni', 'SNI', ['An SNI as returned by the Admin API (spec schema `SNI`).']),
    ('CaCertificate', 'CACertificate', ['A CA Certificate as returned by the Admin API (spec schema `CACertificate`).']),
    ('Vault', 'Vault', ['A Vault as returned by the Admin API (spec schema `Vault`).']),
    ('Key', 'Key', ['A Key (JWK or PEM) as returned by the Admin API (spec schema `Key`).']),
    ('KeySet', 'KeySet', ['A Key Set as returned by the Admin API (spec schema `KeySet`).']),
    ('Workspace', 'Workspace', ['A Workspace as returned by the Admin API (spec schema `Workspace`).']),
]

TAG_ENTRY_SCHEMA = '#/components/responses/TagsResponse/content/application~1json/schema/properties/data'

# --- Resources ---------------------------------------------------------------------------------
M = NS + '\\Models\\'
N = NS + '\\Resources\\Nested\\'


def top(cls, entity, article, schema, model, coll, item, item_arg, item_arg_doc, segment, tag, plural=None, **kw):
    cfg = dict(cls=cls, nested=False, entity=entity, article=article, model=model, input=model + 'Input',
               imports=[M + model, M + model + 'Input'], collection=coll, item=item, item_arg=item_arg,
               item_arg_doc=item_arg_doc, segments=segment, body_arg=kw.pop('body_arg', model[0].lower() + model[1:]),
               body_schema={'POST': schema, 'PATCH': schema, 'PUT': schema},
               doc=[f'{plural or entity + "s"} (spec tag "{tag}"): `{coll}` and `{item}`.'])
    if plural:
        cfg['plural'] = plural
    cfg.update(kw)
    return cfg


def nested(cls, entity, article, schema, model, coll, item, item_arg, item_arg_doc, segment, how, plural=None, **kw):
    cfg = dict(cls=cls, nested=True, entity=entity, article=article, model=model, input=model + 'Input',
               imports=[M + model, M + model + 'Input'], collection=coll, item=item, item_arg=item_arg,
               item_arg_doc=item_arg_doc, segments=segment, body_arg=kw.pop('body_arg', model[0].lower() + model[1:]),
               body_schema={'POST': schema + 'WithoutParents', 'PATCH': schema, 'PUT': schema + 'WithoutParents'},
               doc=[f'{plural or entity + "s"} nested under {how}: `{coll}` and `{item}`.', '', f'Obtain it with `{kw.pop("obtain")}`.'])
    if plural:
        cfg['plural'] = plural
    cfg.update(kw)
    return cfg


def acc(name, cls, arg, arg_doc, doc, path_doc):
    return dict(name=name, cls=cls, arg=arg, arg_doc=arg_doc, doc=doc, path_doc=path_doc, segment_expr='self::SEGMENT')


CONFIGS = [
    top('Consumers', 'Consumer', 'a', 'Consumer', 'Consumer', '/consumers', '/consumers/{ConsumerIdOrUsername}',
        'idOrUsername', 'ID or username', 'consumers', 'Consumers',
        list_query=[('customId', 'custom_id', 'string', 'filter by `custom_id` (spec parameter `CustomId`)')],
        extra_uses=[N + 'ConsumerPlugins'],
        accessors=[acc('plugins', 'ConsumerPlugins', 'consumerId', 'Consumer ID', 'Plugins scoped to one Consumer',
                       '/consumers/{ConsumerIdForNestedEntities}/plugins')]),
    top('Plugins', 'Plugin', 'a', 'Plugin', 'Plugin', '/plugins', '/plugins/{PluginId}', 'id', 'ID', 'plugins', 'Plugins'),
    top('Upstreams', 'Upstream', 'an', 'Upstream', 'Upstream', '/upstreams', '/upstreams/{UpstreamIdOrName}',
        'idOrName', 'ID or name', 'upstreams', 'Upstreams', extra_uses=[N + 'UpstreamTargets'],
        accessors=[acc('targets', 'UpstreamTargets', 'upstreamId', 'Upstream ID', 'Targets of one Upstream',
                       '/upstreams/{UpstreamIdForTarget}/targets')]),
    top('Certificates', 'Certificate', 'a', 'Certificate', 'Certificate', '/certificates', '/certificates/{CertificateId}',
        'id', 'ID', 'certificates', 'Certificates', extra_uses=[N + 'CertificateSnis'],
        accessors=[acc('snis', 'CertificateSnis', 'certificateId', 'Certificate ID', 'SNIs of one Certificate',
                       '/certificates/{CertificateId}/snis')]),
    top('Snis', 'SNI', 'an', 'SNI', 'Sni', '/snis', '/snis/{SNIIdOrName}', 'idOrName', 'ID or name', 'snis', 'SNIs', plural='SNIs'),
    top('CaCertificates', 'CA Certificate', 'a', 'CACertificate', 'CaCertificate', '/ca_certificates',
        '/ca_certificates/{CACertificateId}', 'id', 'ID', 'ca_certificates', 'CA Certificates'),
    top('Vaults', 'Vault', 'a', 'Vault', 'Vault', '/vaults', '/vaults/{VaultIdOrPrefix}', 'idOrPrefix', 'ID or prefix',
        'vaults', 'Vaults'),
    top('Keys', 'Key', 'a', 'Key', 'Key', '/keys', '/keys/{KeyIdOrName}', 'idOrName', 'ID or name', 'keys', 'Keys'),
    top('KeySets', 'Key Set', 'a', 'KeySet', 'KeySet', '/key-sets', '/key-sets/{KeySetIdOrName}', 'idOrName', 'ID or name',
        'key-sets', 'KeySets', extra_uses=[N + 'KeySetKeys'],
        accessors=[acc('keys', 'KeySetKeys', 'keySetIdOrName', 'Key Set ID or name', 'Keys of one Key Set',
                       '/key-sets/{KeySetIdOrName}/keys')]),
    top('Workspaces', 'Workspace', 'a', 'Workspace', 'Workspace', '/workspaces', '/workspaces/{WorkspaceIdOrName}',
        'idOrName', 'ID or name', 'workspaces', 'Workspaces'),
    nested('ServicePlugins', 'Plugin', 'a', 'Plugin', 'Plugin', '/services/{ServiceIdOrName}/plugins',
           '/services/{ServiceIdOrName}/plugins/{PluginId}', 'id', 'ID', 'plugins', 'one Service',
           obtain='$client->services()->plugins($serviceIdOrName)'),
    nested('RoutePlugins', 'Plugin', 'a', 'Plugin', 'Plugin', '/routes/{RouteIdOrName}/plugins',
           '/routes/{RouteIdOrName}/plugins/{PluginId}', 'id', 'ID', 'plugins', 'one Route',
           obtain='$client->routes()->plugins($routeIdOrName)'),
    nested('ConsumerPlugins', 'Plugin', 'a', 'Plugin', 'Plugin', '/consumers/{ConsumerIdForNestedEntities}/plugins',
           '/consumers/{ConsumerIdForNestedEntities}/plugins/{PluginId}', 'id', 'ID', 'plugins', 'one Consumer',
           obtain='$client->consumers()->plugins($consumerId)'),
    nested('UpstreamTargets', 'Target', 'a', 'Target', 'Target', '/upstreams/{UpstreamIdForTarget}/targets',
           '/upstreams/{UpstreamIdForTarget}/targets/{TargetIdOrTarget}', 'idOrTarget', 'ID or target (`host:port`)',
           'targets', 'one Upstream', obtain='$client->upstreams()->targets($upstreamId)'),
    nested('CertificateSnis', 'SNI', 'an', 'SNI', 'Sni', '/certificates/{CertificateId}/snis',
           '/certificates/{CertificateId}/snis/{SNIIdOrName}', 'idOrName', 'ID or name', 'snis', 'one Certificate',
           plural='SNIs', obtain='$client->certificates()->snis($certificateId)'),
    nested('KeySetKeys', 'Key', 'a', 'Key', 'Key', '/key-sets/{KeySetIdOrName}/keys',
           '/key-sets/{KeySetIdOrName}/keys/{KeyIdOrName}', 'idOrName', 'ID or name', 'keys', 'one Key Set',
           obtain='$client->keySets()->keys($keySetIdOrName)'),
]

# --- Tests -------------------------------------------------------------------------------------
FIXTURES = [('Consumer', 'Consumer'), ('Plugin', 'Plugin'), ('Upstream', 'Upstream'), ('Target', 'Target'),
            ('Certificate', 'Certificate'), ('Sni', 'SNI'), ('CaCertificate', 'CACertificate'), ('Vault', 'Vault'),
            ('Key', 'Key'), ('KeySet', 'KeySet'), ('Workspace', 'Workspace'), ('TagEntry', TAG_ENTRY_SCHEMA)]

ACCESSORS = {
    'Consumers': ('$client->consumers()', '', None),
    'Plugins': ('$client->plugins()', '', None),
    'Upstreams': ('$client->upstreams()', '', None),
    'Certificates': ('$client->certificates()', '', None),
    'Snis': ('$client->snis()', '', None),
    'CaCertificates': ('$client->caCertificates()', '', None),
    'Vaults': ('$client->vaults()', '', None),
    'Keys': ('$client->keys()', '', None),
    'KeySets': ('$client->keySets()', '', None),
    'Workspaces': ('$client->workspaces()', '', None),
    'ServicePlugins': ("$client->services()->plugins('parent 1')", '/services/parent%201', 'Services'),
    'RoutePlugins': ("$client->routes()->plugins('parent 1')", '/routes/parent%201', 'Routes'),
    'ConsumerPlugins': ("$client->consumers()->plugins('parent 1')", '/consumers/parent%201', 'Consumers'),
    'UpstreamTargets': ("$client->upstreams()->targets('parent 1')", '/upstreams/parent%201', 'Upstreams'),
    'CertificateSnis': ("$client->certificates()->snis('parent 1')", '/certificates/parent%201', 'Certificates'),
    'KeySetKeys': ("$client->keySets()->keys('parent 1')", '/key-sets/parent%201', 'KeySets'),
}

EMPTY_PARENT = """it('rejects an empty parent ID before sending anything', function (): void {
    expect(fn (): {cls} => MockKong::queue()->client->{chain}(''))
        ->toThrow(InvalidArgumentException::class, '{label} must not be empty.');
});"""

CUSTOM_ID = """it('filters by custom_id on list and all', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, ['data' => [Fixture::get('consumer')]]),
        MockKong::json(200, ['data' => [Fixture::get('consumer')]]),
    );

    {fn}($kong->client)->list(customId: 'crm-42');
    iterator_to_array({fn}($kong->client)->all(new ListOptions(size: 10), 'crm-42'));

    expect($kong->queryAt(0))->toBe(['custom_id' => 'crm-42'])
        ->and($kong->queryAt(1))->toBe(['size' => '10', 'custom_id' => 'crm-42']);
});"""

LABELS = {'ServicePlugins': ('services()->plugins', 'Service ID or name'), 'RoutePlugins': ('routes()->plugins', 'Route ID or name'),
          'ConsumerPlugins': ('consumers()->plugins', 'Consumer ID'), 'UpstreamTargets': ('upstreams()->targets', 'Upstream ID'),
          'CertificateSnis': ('certificates()->snis', 'Certificate ID'), 'KeySetKeys': ('keySets()->keys', 'Key Set ID or name')}


def main():
    for cls, schema, doc in ENTITIES:
        generate_entity(cls, schema, doc, NESTED, ENUMS)
    generate_entity('TagEntry', TAG_ENTRY_SCHEMA,
                    ['One entity/tag pair listed by `GET /tags` and `GET /tags/{tag}` (spec response `TagsResponse`).'],
                    NESTED, ENUMS, with_input=False)

    for cfg in CONFIGS:
        resource(cfg)

    for cls, schema in FIXTURES:
        fixture(snake(cls), schema)

    for cfg in CONFIGS:
        expr, parent_path, parent = ACCESSORS[cfg['cls']]
        extra = []
        if cfg['cls'] in LABELS:
            chain, label = LABELS[cfg['cls']]
            extra.append(EMPTY_PARENT.replace('{cls}', cfg['cls']).replace('{chain}', chain).replace('{label}', label))
        if cfg['cls'] == 'Consumers':
            extra.append(CUSTOM_ID)
        cfg = {**cfg, 'test_extra': extra}
        if parent:
            cfg['test_uses'] = [NS + '\\Resources\\' + parent]
        feature_test(cfg, expr, parent_path, covers_extra=[parent] if parent else [])

    finalize()


if __name__ == '__main__':
    main()
