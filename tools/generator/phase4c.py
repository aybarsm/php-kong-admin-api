"""Phase 4c: Enterprise entities (spec tags Cloned Plugins, CustomPlugins, Degraphql_routes, GraphQL Cost
Decorations, OIDC JWKs, Groups, RBAC*, Partials, Partial Links).

Generates the uniform CRUD entities and the global (`/rbac_*`) and workspace-only (`/{workspace}/rbac/...`)
RBAC resources, plus the Partial variant DTOs. Hand-written (not touched here): Models\\Partial,
Models\\PartialFactory, Resources\\Partials, Nested\\PartialLinks, the Admins/Licenses/EventHooks/
WorkspaceGroups resources, Nested\\GroupRoles, Nested\\WorkspaceRbacUserRoles,
Nested\\WorkspaceRbacRoleEndpoints, and the KongClient/Services accessors.

Run from the repo root:  python3 tools/generator/phase4c.py
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
PARTIALS = [('PartialRedisCe', 'redis-ce'), ('PartialRedisEe', 'redis-ee'), ('PartialVectordb', 'vectordb'),
            ('PartialEmbeddings', 'embeddings'), ('PartialModel', 'model')]

# Partial `config` stays a plain array in v1 (spec-notes Q13).
models.FREEFORM.update((cls, 'config') for cls, _ in PARTIALS)

# Spec objects describing dynamic maps by example (event-hook sources) stay plain arrays (spec-notes Q17).
models.FREEFORM.update({('EventHookSources', 'data'), ('EventHookSourceEvents', 'data')})

ENUMS = {
    ('RbacUserRole', 'role_source'): 'RbacRoleSource',
    ('LicenseReport', 'deployment_info.type'): 'DeploymentType',
}

NESTED = {
    ('OidcJwk', 'jwks'): 'OidcJwkSet',
    ('OidcJwk', 'jwks.keys[]'): 'OidcJwkKey',
    ('GroupRole', 'group'): 'GroupRoleGroup',
    ('GroupRole', 'rbac_role'): 'GroupRoleRbacRole',
    ('AdminRoles', 'roles[]'): 'AdminRole',
    ('LicenseReport', 'counters'): 'LicenseReportCounters',
    ('LicenseReport', 'counters.buckets[]'): 'LicenseReportBucket',
    ('LicenseReport', 'deployment_info'): 'LicenseReportDeployment',
    ('LicenseReport', 'license'): 'LicenseReportLicense',
    ('LicenseReport', 'plugins_count'): 'LicenseReportPluginsCount',
    ('LicenseReport', 'plugins_count.tiers'): 'LicenseReportPluginTiers',
    ('LicenseReport', 'system_info'): 'LicenseReportSystemInfo',
    ('EventHook', 'config'): 'EventHookConfig',
    ('EventHook', 'config.headers'): 'EventHookConfigHeaders',
    ('EventHook', 'config.payload'): 'EventHookConfigPayload',
    ('Webhook', 'config.headers'): 'WebhookHeaders',
    ('WorkspaceGroupRole', 'group'): 'WorkspaceGroupRoleGroup',
    ('WorkspaceGroupRole', 'rbac_role'): 'WorkspaceGroupRoleRbacRole',
}

R = '#/components/responses/'
RB = '#/components/requestBodies/'
JSON = '/content/application~1json/schema'

ENTITIES = [
    ('ClonedPlugin', 'ClonedPlugin', ['A Cloned Plugin as returned by the Admin API (spec schema `ClonedPlugin`).']),
    ('CustomPlugin', 'CustomPlugin', ['A Custom Plugin as returned by the Admin API (spec schema `CustomPlugin`).']),
    ('DegraphqlRoute', 'Degraphql_route',
     ['A DeGraphQL route as returned by the Admin API (spec schema `Degraphql_route`).']),
    ('GraphqlCostDecoration', 'GraphQLCostDecoration',
     ['A GraphQL cost decoration as returned by the Admin API (spec schema `GraphQLCostDecoration`).']),
    ('OidcJwk', 'OidcJwk', ['An OpenID Connect JWK set as returned by the Admin API (spec schema `OidcJwk`).']),
    ('Group', 'Group', ['A Group as returned by the Admin API (spec schema `Group`).']),
    ('RbacGroupRole', 'RBACGroupRole', ['An RBAC group-role assignment as returned by the Admin API (spec schema `RBACGroupRole`).']),
    ('RbacRole', 'RBACRole', ['An RBAC Role as returned by the Admin API (spec schema `RBACRole`).']),
    ('RbacRoleEndpoint', 'RBACRoleEndpoint',
     ['An RBAC Role endpoint permission as returned by the Admin API (spec schema `RBACRoleEndpoint`).']),
    ('RbacRoleEntity', 'RBACRoleEntity',
     ['An RBAC Role entity permission as returned by the Admin API (spec schema `RBACRoleEntity`).']),
    ('RbacUser', 'RBACUser', ['An RBAC User as returned by the Admin API (spec schema `RBACUser`).', '',
                              'The write-only `user_token` is available on RbacUserInput only.']),
    ('RbacUserGroup', 'RBACUserGroup',
     ['An RBAC user-group assignment as returned by the Admin API (spec schema `RBACUserGroup`).']),
    ('RbacUserRole', 'RBACUserRole',
     ['An RBAC user-role assignment as returned by the Admin API (spec schema `RBACUserRole`).']),
    ('Admin', 'Admin', ['An Admin as returned by the Admin API (spec schema `Admin`).']),
    ('GroupRole', 'GroupRole', ['A role assigned to a Group (spec schema `GroupRole`, also the `GroupRoleRequest` body).']),
] + [
    (cls, cls, [f'A `{type_}` Partial as returned by the Admin API (spec schema `{cls}`).', '',
                'One variant of the spec\'s `Partial` oneOf (discriminator `type`). `config` is kept as a plain',
                'array in v1 (spec-notes Q13).'])
    for cls, type_ in PARTIALS
]

OUTPUTS = [
    ('PartialLink', 'PartialLink', ['A Plugin linked to a Partial (spec schema `PartialLink`).']),
    ('AdminSummary', R + 'ListAdminsResponse' + JSON + '/properties/data',
     ['An Admin as listed by `GET /admins` (spec response `ListAdminsResponse`).', '',
      '`PATCH /admins/{adminNameOrId}/workspaces/{workspaceNameOrId}` returns the same properties inline.']),
    ('AdminRoles', R + 'AdminRolesCreated' + JSON,
     ['The roles assigned to an Admin (spec response `AdminRolesCreated`).']),
    ('License', R + 'LicenseResponse' + JSON, ['A License as returned by the Admin API (spec response `LicenseResponse`).']),
    ('LicenseReport', R + 'ReportResponse' + JSON, ['The license report of `GET /license/report` (spec response `ReportResponse`).']),
    ('EventHook', '#/components/schemas/Event-Hooks/properties/data',
     ['An Event Hook as listed by the Admin API (items of spec schema `Event-Hooks`).']),
    ('EventHookSources', R + 'ListSourcesResponse' + JSON,
     ['The event-hook sources of `GET /event-hooks/sources` (spec response `ListSourcesResponse`).', '',
      '`data` is kept as a plain array: the spec describes this dynamic map by example (spec-notes Q17).']),
    ('EventHookSourceEvents', R + 'ListSourceEventsResponse' + JSON,
     ['The events of one source from `GET /event-hooks/sources/{source}` (spec response `ListSourceEventsResponse`).', '',
      '`data` is kept as a plain array: the spec describes this dynamic map by example (spec-notes Q17).']),
    ('WorkspaceGroup', R + 'ListAllGroups' + JSON,
     ['A group listed by `GET /workspace_/groups` (spec response `ListAllGroups`).', '',
      '`POST /workspace_/groups` returns the same properties (spec response `CreateGroupsResponse`).']),
    ('WorkspaceGroupRole', R + 'GetRolesResponse' + JSON,
     ['A role assigned to a workspace group (spec response `GetRolesResponse`).', '',
      '`POST /workspace_/groups/{groups}/roles` returns the same shape (`GroupRoleAssociationCreated`).']),
]

# Input-only DTOs for request bodies: (class without the Input suffix, pointer, doc)
INPUTS = [
    ('AdminCreation', RB + 'AdminCreationRequest' + JSON, ['Request body for `POST /admins` (spec request body `AdminCreationRequest`).']),
    ('AdminRegistration', RB + 'AdminCredentialRegistrationRequest' + JSON,
     ['Request body for `POST /admins/register` (spec request body `AdminCredentialRegistrationRequest`).']),
    ('AdminPasswordResetRequest', RB + 'AdminPasswordResetRequest' + JSON,
     ['Request body for `POST /admins/password_resets` (spec request body `AdminPasswordResetRequest`).']),
    ('AdminPasswordReset', RB + 'AdminPasswordResetConfirmationRequest' + JSON,
     ['Request body for `PATCH /admins/password_resets` (spec request body `AdminPasswordResetConfirmationRequest`).']),
    ('AdminRoles', RB + 'AdminRoleUpdateRequest' + JSON,
     ['Request body for `POST /admins/{adminNameOrId}/roles` (spec request body `AdminRoleUpdateRequest`).']),
    ('License', RB + 'LicenseRequest' + JSON,
     ['Request body for creating or updating a License (spec request body `LicenseRequest`).']),
    ('Webhook', RB + 'AddWebhook' + JSON,
     ['Request body for `POST /event-hooks` (spec request body `AddWebhook`).', '',
      'The spec names `config.*` properties with literal dots; they are sent exactly as named.']),
    ('WorkspaceGroup', RB + 'UpdateGroupsRequest' + JSON,
     ['Request body for creating or renaming a workspace group (spec request body `UpdateGroupsRequest`).']),
]

# --- Resources ---------------------------------------------------------------------------------
SEG_COSTS = ["'graphql-rate-limiting-advanced'", "'costs'"]


def ws_top(cls, entity, article, schema, model, coll, item, item_arg, item_arg_doc, segments, tag, **kw):
    """A `/{workspace}`-only top-level resource (always prefixed; spec default workspace `default`)."""
    cfg = top(cls, entity, article, schema, model, coll, item, item_arg, item_arg_doc, '/'.join(segments), tag, **kw)
    cfg['segment_list'] = [f"'{s}'" for s in segments]
    cfg['doc'] = [f'{entity}s of the current workspace (spec tag "{tag}"): `{coll}` and `{item}`.', '',
                  'These paths exist only under `/{workspace}`; without a workspace the spec default `default` is used.']
    return cfg


CONFIGS = [
    top('ClonedPlugins', 'Cloned Plugin', 'a', 'ClonedPlugin', 'ClonedPlugin', '/cloned-plugins',
        '/cloned-plugins/{ClonedPluginIdOrName}', 'idOrName', 'ID or name', 'cloned-plugins', 'Cloned Plugins'),
    top('CustomPlugins', 'Custom Plugin', 'a', 'CustomPlugin', 'CustomPlugin', '/custom-plugins',
        '/custom-plugins/{CustomPluginIdOrName}', 'idOrName', 'ID or name', 'custom-plugins', 'CustomPlugins'),
    top('DegraphqlRoutes', 'DeGraphQL route', 'a', 'Degraphql_route', 'DegraphqlRoute', '/degraphql_routes',
        '/degraphql_routes/{Degraphql_routeIdOrName}', 'idOrName', 'ID or name', 'degraphql_routes', 'Degraphql_routes'),
    {**top('GraphqlCostDecorations', 'GraphQL cost decoration', 'a', 'GraphQLCostDecoration', 'GraphqlCostDecoration',
           '/graphql-rate-limiting-advanced/costs', '/graphql-rate-limiting-advanced/costs/{GraphQLCostDecorationId}',
           'id', 'ID', 'graphql-rate-limiting-advanced/costs', 'GraphQL Cost Decorations'), 'segment_list': SEG_COSTS},
    top('OidcJwks', 'OIDC JWK set', 'an', 'OidcJwk', 'OidcJwk', '/oic_jwks', '/oic_jwks/{OidcJwkId}', 'id', 'ID',
        'oic_jwks', 'OIDC JWKs'),
    top('Groups', 'Group', 'a', 'Group', 'Group', '/groups', '/groups/{GroupId}', 'id', 'ID', 'groups', 'Groups',
        extra_uses=[N + 'GroupRoles'],
        accessors=[acc('roles', 'GroupRoles', 'groupId', 'Group ID', 'RBAC roles of one Group',
                       '/groups/{GroupId}/roles')]),
    top('GroupRbacRoles', 'group RBAC role', 'a', 'RBACGroupRole', 'RbacGroupRole', '/group_rbac_roles',
        '/group_rbac_roles/{RBACGroupRoleId}', 'id', 'ID', 'group_rbac_roles', 'RBACGroupRoles'),
    top('RbacUsers', 'RBAC user', 'an', 'RBACUser', 'RbacUser', '/rbac_users', '/rbac_users/{RBACUserId}', 'id', 'ID',
        'rbac_users', 'RBACUsers'),
    top('RbacRoles', 'RBAC role', 'an', 'RBACRole', 'RbacRole', '/rbac_roles', '/rbac_roles/{RBACRoleId}', 'id', 'ID',
        'rbac_roles', 'RBACRoles'),
    top('RbacRoleEndpoints', 'RBAC role endpoint', 'an', 'RBACRoleEndpoint', 'RbacRoleEndpoint', '/rbac_role_endpoints',
        '/rbac_role_endpoints/{RBACRoleEndpointId}', 'id', 'ID', 'rbac_role_endpoints', 'RBACRoleEndpoints'),
    top('RbacRoleEntities', 'RBAC role entity', 'an', 'RBACRoleEntity', 'RbacRoleEntity', '/rbac_role_entities',
        '/rbac_role_entities/{RBACRoleEntityId}', 'id', 'ID', 'rbac_role_entities', 'RBACRoleEntities',
        plural='RBAC role entities'),
    top('RbacUserGroups', 'RBAC user group', 'an', 'RBACUserGroup', 'RbacUserGroup', '/rbac_user_groups',
        '/rbac_user_groups/{RBACUserGroupId}', 'id', 'ID', 'rbac_user_groups', 'RBACUserGroups'),
    top('RbacUserRoles', 'RBAC user role', 'an', 'RBACUserRole', 'RbacUserRole', '/rbac_user_roles',
        '/rbac_user_roles/{RBACUserRoleId}', 'id', 'ID', 'rbac_user_roles', 'RBACUserRoles'),
    ws_top('WorkspaceRbacUsers', 'RBAC user', 'an', 'RBACUser', 'RbacUser', '/{workspace}/rbac/users',
           '/{workspace}/rbac/users/{RBACUserId}', 'id', 'ID', ['rbac', 'users'], 'RBACUsers',
           extra_uses=[N + 'WorkspaceRbacUserGroups', N + 'WorkspaceRbacUserRoles'],
           accessors=[{**acc('groups', 'WorkspaceRbacUserGroups', 'userId', 'RBAC user ID', 'Groups of one RBAC user',
                             '/{workspace}/rbac/users/{RBACUserIdForNestedEntities}/groups'),
                       'segment_expr': "'rbac', 'users'"},
                      {**acc('roles', 'WorkspaceRbacUserRoles', 'userId', 'RBAC user ID', 'Roles of one RBAC user',
                             '/{workspace}/rbac/users/{RBACUserIdForNestedEntities}/roles'),
                       'segment_expr': "'rbac', 'users'"}]),
    ws_top('WorkspaceRbacRoles', 'RBAC role', 'an', 'RBACRole', 'RbacRole', '/{workspace}/rbac/roles',
           '/{workspace}/rbac/roles/{RBACRoleId}', 'id', 'ID', ['rbac', 'roles'], 'RBACRoles',
           extra_uses=[N + 'WorkspaceRbacRoleEntities', N + 'WorkspaceRbacRoleEndpoints'],
           accessors=[{**acc('entities', 'WorkspaceRbacRoleEntities', 'roleId', 'RBAC role ID',
                             'Entity permissions of one RBAC role',
                             '/{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/entities'),
                       'segment_expr': "'rbac', 'roles'"},
                      {**acc('endpoints', 'WorkspaceRbacRoleEndpoints', 'roleId', 'RBAC role ID',
                             'Endpoint permissions of one RBAC role',
                             '/{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/endpoints'),
                       'segment_expr': "'rbac', 'roles'"}]),
    {**nested('ServiceDegraphqlRoutes', 'DeGraphQL route', 'a', 'Degraphql_route', 'DegraphqlRoute',
              '/services/{ServiceIdOrName}/degraphql/routes',
              '/services/{ServiceIdOrName}/degraphql/routes/{Degraphql_routeIdOrName}', 'idOrName', 'ID or name',
              'degraphql/routes', 'one Service', obtain='$client->services()->degraphqlRoutes($serviceIdOrName)'),
     'segment_list': ["'degraphql'", "'routes'"]},
    {**nested('ServiceGraphqlCostDecorations', 'GraphQL cost decoration', 'a', 'GraphQLCostDecoration',
              'GraphqlCostDecoration', '/services/{ServiceIdOrName}/graphql-rate-limiting-advanced/costs',
              '/services/{ServiceIdOrName}/graphql-rate-limiting-advanced/costs/{GraphQLCostDecorationId}', 'id', 'ID',
              'graphql-rate-limiting-advanced/costs', 'one Service',
              obtain='$client->services()->graphqlCostDecorations($serviceIdOrName)'),
     'segment_list': SEG_COSTS},
    {**nested('WorkspaceRbacUserGroups', 'RBAC user group', 'an', 'RBACUserGroup', 'RbacUserGroup',
              '/{workspace}/rbac/users/{RBACUserIdForNestedEntities}/groups',
              '/{workspace}/rbac/users/{RBACUserIdForNestedEntities}/groups/{RBACUserGroupId}', 'id', 'ID', 'groups',
              'one RBAC user (workspace-only paths)', obtain='$client->workspaceRbacUsers()->groups($userId)'),
     'body_schema': {'POST': 'RBACUserGroup', 'PATCH': 'RBACUserGroup', 'PUT': 'RBACUserGroup'}},
    {**nested('WorkspaceRbacRoleEntities', 'RBAC role entity', 'an', 'RBACRoleEntity', 'RbacRoleEntity',
              '/{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/entities',
              '/{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/entities/{RBACRoleEntityId}', 'id', 'ID',
              'entities', 'one RBAC role (workspace-only paths)', plural='RBAC role entities',
              obtain='$client->workspaceRbacRoles()->entities($roleId)'),
     'body_schema': {'POST': 'RBACRoleEntity', 'PATCH': 'RBACRoleEntity', 'PUT': 'RBACRoleEntity'}},
]

# --- Tests -------------------------------------------------------------------------------------
FIXTURES = [(cls, schema) for cls, schema, _ in ENTITIES] + [(cls, schema) for cls, schema, _ in OUTPUTS]

ACCESSORS = {
    'ClonedPlugins': ('$client->clonedPlugins()', '', None),
    'CustomPlugins': ('$client->customPlugins()', '', None),
    'DegraphqlRoutes': ('$client->degraphqlRoutes()', '', None),
    'GraphqlCostDecorations': ('$client->graphqlCostDecorations()', '', None),
    'OidcJwks': ('$client->oidcJwks()', '', None),
    'Groups': ('$client->groups()', '', None),
    'GroupRbacRoles': ('$client->groupRbacRoles()', '', None),
    'RbacUsers': ('$client->rbacUsers()', '', None),
    'RbacRoles': ('$client->rbacRoles()', '', None),
    'RbacRoleEndpoints': ('$client->rbacRoleEndpoints()', '', None),
    'RbacRoleEntities': ('$client->rbacRoleEntities()', '', None),
    'RbacUserGroups': ('$client->rbacUserGroups()', '', None),
    'RbacUserRoles': ('$client->rbacUserRoles()', '', None),
    'WorkspaceRbacUsers': ('$client->workspaceRbacUsers()', '', None),
    'WorkspaceRbacRoles': ('$client->workspaceRbacRoles()', '', None),
    'ServiceDegraphqlRoutes': ("$client->services()->degraphqlRoutes('parent 1')", '/services/parent%201', 'Services'),
    'ServiceGraphqlCostDecorations': ("$client->services()->graphqlCostDecorations('parent 1')",
                                      '/services/parent%201', 'Services'),
    'WorkspaceRbacUserGroups': ("$client->workspaceRbacUsers()->groups('parent 1')", '/rbac/users/parent%201',
                                'WorkspaceRbacUsers'),
    'WorkspaceRbacRoleEntities': ("$client->workspaceRbacRoles()->entities('parent 1')", '/rbac/roles/parent%201',
                                  'WorkspaceRbacRoles'),
}
LABELS = {
    'ServiceDegraphqlRoutes': ('services()->degraphqlRoutes', 'Service ID or name'),
    'ServiceGraphqlCostDecorations': ('services()->graphqlCostDecorations', 'Service ID or name'),
    'WorkspaceRbacUserGroups': ('workspaceRbacUsers()->groups', 'RBAC user ID'),
    'WorkspaceRbacRoleEntities': ('workspaceRbacRoles()->entities', 'RBAC role ID'),
}


def main():
    partials = {cls for cls, _ in PARTIALS}
    for cls, schema, doc in ENTITIES:
        # Partial variants implement the hand-written Models\Partial interface (dispatched by PartialFactory).
        generate_entity(cls, schema, doc, NESTED, ENUMS, contract='Partial' if cls in partials else None)
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
        cfg = {**cfg, 'test_extra': extra}
        if parent:
            cfg['test_uses'] = [NS + '\\Resources\\' + parent]
        feature_test(cfg, expr, parent_path, covers_extra=[parent] if parent else [])

    finalize()


if __name__ == '__main__':
    main()
