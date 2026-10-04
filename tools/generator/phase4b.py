"""Phase 4b: consumer credentials and consumer groups (spec tags ACLs, API-keys, Basic-auth credentials,
HMAC-auth credentials, JWTs, MTLS-auth credentials, Consumer Groups).

Generates the credential and Consumer Group DTOs/resources (top-level and nested under consumers), the
Consumer Group plugins resource, the membership/override response DTOs and the inline request-body inputs.
Hand-written (not touched here): Nested\\ConsumerGroupConsumers, Nested\\ConsumerConsumerGroups,
Nested\\ConsumerGroupRateLimitingAdvancedOverride, Enums\\RateLimitWindowType and the KongClient accessors.

Run from the repo root:  python3 tools/generator/phase4b.py
"""
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent))

import models  # noqa: E402
from feature_tests import feature_test, snake  # noqa: E402
from fixtures import fixture  # noqa: E402
from models import NS, finalize, generate_entity  # noqa: E402
from phase4a import EMPTY_PARENT, M, N, acc, nested, top  # noqa: E402
from resources import resource  # noqa: E402

# --- Models ------------------------------------------------------------------------------------
models.REFS.update({'Consumer': 'Consumer', 'ConsumerGroup': 'ConsumerGroup'})

# Secrets the spec doesn't mark x-encrypted (spec-notes Q18).
models.SENSITIVE.update({('KeyAuth', 'key'), ('Jwt', 'secret')})

ENUMS = {
    ('Jwt', 'algorithm'): 'JwtAlgorithm',
    ('RateLimitingAdvancedOverride', 'config.window_type'): 'RateLimitWindowType',
}

NESTED = {
    ('RateLimitingAdvancedOverride', 'config'): 'RateLimitingAdvancedOverrideConfig',
}

P_GROUP_CONSUMERS = '#/paths/~1consumer_groups~1{ConsumerGroupId}~1consumers/post'
P_CONSUMER_GROUPS = '#/paths/~1consumers~1{ConsumerIdOrUsername}~1consumer_groups/post'
P_OVERRIDE = '#/paths/~1consumer_groups~1{ConsumerGroupId}~1overrides~1plugins~1rate-limiting-advanced/put'
JSON = '/content/application~1json/schema'

ENTITIES = [
    ('Acl', 'ACL', ['An ACL (Consumer group membership for the ACL plugin) as returned by the Admin API (spec schema `ACL`).']),
    ('KeyAuth', 'KeyAuth', ['An API key (key-auth credential) as returned by the Admin API (spec schema `KeyAuth`).']),
    ('BasicAuth', 'BasicAuth', ['A Basic-auth credential as returned by the Admin API (spec schema `BasicAuth`).']),
    ('HmacAuth', 'HMACAuth', ['An HMAC-auth credential as returned by the Admin API (spec schema `HMACAuth`).']),
    ('Jwt', 'JWT', ['A JWT credential as returned by the Admin API (spec schema `JWT`).']),
    ('MtlsAuth', 'MTLSAuth', ['An MTLS-auth credential as returned by the Admin API (spec schema `MTLSAuth`).']),
    ('ConsumerGroup', 'ConsumerGroup', ['A Consumer Group as returned by the Admin API (spec schema `ConsumerGroup`).']),
]

# Output-only DTOs: (class, schema or pointer, doc)
OUTPUTS = [
    ('ConsumerGroupInsideWrapper', 'ConsumerGroupInsideWrapper',
     ['The response of `GET /consumer_groups/{ConsumerGroupId}` (spec schema `ConsumerGroupInsideWrapper`).', '',
      'The spec wraps the group in `consumer_group` and defines no other field, also when `list_consumers` is',
      'sent (spec-notes Q9).']),
    ('ConsumerGroupMembership', P_GROUP_CONSUMERS + '/responses/201' + JSON,
     ['The response of adding a Consumer to a Consumer Group (`POST /consumer_groups/{ConsumerGroupId}/consumers`).']),
    ('ConsumerMembership', P_CONSUMER_GROUPS + '/responses/201' + JSON,
     ['The response of adding a Consumer to Consumer Groups (`POST /consumers/{ConsumerIdOrUsername}/consumer_groups`).']),
    ('RateLimitingAdvancedOverride', P_OVERRIDE + '/responses/201' + JSON,
     ['The rate-limiting-advanced override of a Consumer Group as returned by',
      '`PUT /consumer_groups/{ConsumerGroupId}/overrides/plugins/rate-limiting-advanced`.']),
]

# Input-only DTOs for inline request bodies: (class without the Input suffix, pointer, doc)
INPUTS = [
    ('ConsumerGroupMember', P_GROUP_CONSUMERS + '/requestBody' + JSON,
     ['Request body for adding a Consumer to a Consumer Group (`POST /consumer_groups/{ConsumerGroupId}/consumers`).']),
    ('ConsumerGroupAssignment', P_CONSUMER_GROUPS + '/requestBody' + JSON,
     ['Request body for adding a Consumer to a Consumer Group (`POST /consumers/{ConsumerIdOrUsername}/consumer_groups`).']),
    ('RateLimitingAdvancedOverride', '#/components/requestBodies/consumerGroupsConfigResponse' + JSON,
     ['Request body for `PUT /consumer_groups/{ConsumerGroupId}/overrides/plugins/rate-limiting-advanced`',
      '(spec request body `consumerGroupsConfigResponse`).', '',
      'The spec names these properties with literal dots (`config.limit`, …) and types them as strings,',
      'while the 201 response nests them under `config` as integers. They are sent exactly as the spec',
      'names them; pass an array to send another shape (spec-notes Q10).']),
]

# --- Resources ---------------------------------------------------------------------------------
CREDENTIALS = [
    # resource suffix, entity, article, schema, model, top path, top item param, nested segment, nested item param, tag
    ('Acls', 'ACL', 'an', 'ACL', 'Acl', 'acls', 'ACLId', 'acls', 'ACLs'),
    ('KeyAuths', 'API key', 'an', 'KeyAuth', 'KeyAuth', 'key-auths', 'KeyAuthId', 'key-auth', 'API-keys'),
    ('BasicAuths', 'Basic-auth credential', 'a', 'BasicAuth', 'BasicAuth', 'basic-auths', 'BasicAuthId', 'basic-auth',
     'Basic-auth credentials'),
    ('HmacAuths', 'HMAC-auth credential', 'an', 'HMACAuth', 'HmacAuth', 'hmac-auths', 'HMACAuthId', 'hmac-auth',
     'HMAC-auth credentials'),
    ('Jwts', 'JWT', 'a', 'JWT', 'Jwt', 'jwts', 'JWTId', 'jwt', 'JWTs'),
    ('MtlsAuths', 'MTLS-auth credential', 'an', 'MTLSAuth', 'MtlsAuth', 'mtls-auths', 'MTLSAuthId', 'mtls-auth',
     'MTLS-auth credentials'),
]

CONFIGS = []
for res, entity, article, schema, model, seg, param, nseg, tag in CREDENTIALS:
    CONFIGS.append(top(res, entity, article, schema, model, f'/{seg}', f'/{seg}/{{{param}}}', 'id', 'ID', seg, tag,
                       plural=entity + 's'))
    CONFIGS.append(nested('Consumer' + res, entity, article, schema, model,
                          f'/consumers/{{ConsumerIdForNestedEntities}}/{nseg}',
                          f'/consumers/{{ConsumerIdForNestedEntities}}/{nseg}/{{{param}}}', 'id', 'ID', nseg,
                          'one Consumer', plural=entity + 's',
                          obtain=f'$client->consumers()->{res[0].lower() + res[1:]}($consumerId)'))

CONFIGS += [
    top('ConsumerGroups', 'Consumer Group', 'a', 'ConsumerGroup', 'ConsumerGroup', '/consumer_groups',
        '/consumer_groups/{ConsumerGroupId}', 'id', 'ID', 'consumer_groups', 'Consumer Groups',
        get_returns='ConsumerGroupInsideWrapper', get_mapone='ConsumerGroupInsideWrapper::fromArray',
        get_query=[('listConsumers', 'list_consumers', 'bool',
                    'expand the group with its consumers (spec parameter `ListConsumers`)')],
        get_doc=['Returns the spec wrapper `{consumer_group}` (spec-notes Q9).'],
        imports=[M + 'ConsumerGroup', M + 'ConsumerGroupInput', M + 'ConsumerGroupInsideWrapper'],
        extra_uses=[N + 'ConsumerGroupConsumers', N + 'ConsumerGroupPlugins',
                    N + 'ConsumerGroupRateLimitingAdvancedOverride'],
        accessors=[
            acc('consumers', 'ConsumerGroupConsumers', 'groupIdOrName', 'Consumer Group ID or name',
                'Consumers in one Consumer Group', '/consumer_groups/{ConsumerGroupId}/consumers'),
            acc('plugins', 'ConsumerGroupPlugins', 'groupId', 'Consumer Group ID', 'Plugins scoped to one Consumer Group',
                '/consumer_groups/{ConsumerGroupId}/plugins'),
            acc('rateLimitingAdvancedOverride', 'ConsumerGroupRateLimitingAdvancedOverride', 'groupId',
                'Consumer Group ID', 'The rate-limiting-advanced override of one Consumer Group',
                '/consumer_groups/{ConsumerGroupId}/overrides/plugins/rate-limiting-advanced'),
        ]),
    nested('ConsumerGroupPlugins', 'Plugin', 'a', 'Plugin', 'Plugin', '/consumer_groups/{ConsumerGroupId}/plugins',
           '/consumer_groups/{ConsumerGroupId}/plugins/{PluginId}', 'id', 'ID', 'plugins', 'one Consumer Group',
           obtain='$client->consumerGroups()->plugins($groupId)'),
]

# --- Tests -------------------------------------------------------------------------------------
FIXTURES = [('Acl', 'ACL'), ('KeyAuth', 'KeyAuth'), ('BasicAuth', 'BasicAuth'), ('HmacAuth', 'HMACAuth'), ('Jwt', 'JWT'),
            ('MtlsAuth', 'MTLSAuth'), ('ConsumerGroup', 'ConsumerGroup')] + [(c, s) for c, s, _ in OUTPUTS]

ACCESSORS = {'ConsumerGroups': ('$client->consumerGroups()', '', None),
             'ConsumerGroupPlugins': ("$client->consumerGroups()->plugins('parent 1')", '/consumer_groups/parent%201',
                                      'ConsumerGroups')}
LABELS = {'ConsumerGroupPlugins': ('consumerGroups()->plugins', 'Consumer Group ID')}
for res, entity, *_ in CREDENTIALS:
    ACCESSORS[res] = (f'$client->{res[0].lower() + res[1:]}()', '', None)
    ACCESSORS['Consumer' + res] = (f"$client->consumers()->{res[0].lower() + res[1:]}('parent 1')",
                                   '/consumers/parent%201', 'Consumers')
    LABELS['Consumer' + res] = (f'consumers()->{res[0].lower() + res[1:]}', 'Consumer ID')

LIST_CONSUMERS = """it('sends list_consumers on get when asked', function (): void {
    $kong = MockKong::queue(
        MockKong::json(200, Fixture::get('consumer_group_inside_wrapper')),
        MockKong::json(200, Fixture::get('consumer_group_inside_wrapper')),
        MockKong::json(200, Fixture::get('consumer_group_inside_wrapper')),
    );

    {fn}($kong->client)->get('g1', true);
    {fn}($kong->client)->get('g1', false);
    {fn}($kong->client)->get('g1');

    expect($kong->queryAt(0))->toBe(['list_consumers' => 'true'])
        ->and($kong->queryAt(1))->toBe(['list_consumers' => 'false'])
        ->and($kong->queryAt(2))->toBe([]);
});"""


def main():
    for cls, schema, doc in ENTITIES:
        generate_entity(cls, schema, doc, NESTED, ENUMS)
    for cls, schema, doc in OUTPUTS:
        generate_entity(cls, schema, doc, NESTED, ENUMS, with_input=False)
    for cls, schema, doc in INPUTS:
        generate_entity(cls, schema, doc, NESTED, ENUMS, input_doc=doc, with_output=False)

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
        if cfg['cls'] == 'ConsumerGroups':
            extra.append(LIST_CONSUMERS)
        cfg = {**cfg, 'test_extra': extra}
        if parent:
            cfg['test_uses'] = [NS + '\\Resources\\' + parent]
        feature_test(cfg, expr, parent_path, covers_extra=[parent] if parent else [])

    finalize()


if __name__ == '__main__':
    main()
