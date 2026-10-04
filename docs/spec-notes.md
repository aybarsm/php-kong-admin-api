# Spec notes: Kong Admin API v3.16 (`resources/kong-admin-api/v3.16.json`)

This is a register of anomalies, open questions and blocked operations in the spec.
Entries come from the spec. Where a decision used an approved secondary source (Kong's open-source code, Q4–Q6), it says so.
Every question below was reviewed and decided with the maintainer on 2026-10-04. New anomalies are added as **open** until decided.

## JSON vs YAML (accepted 2026-10-04)

`v3.16.yaml` writes 48 large integers in scientific notation (e.g. `created_at: 1.627588552e+09`), so they parse as floats.
All values are numerically identical to `v3.16.json`, and none of the differences touch paths, methods, parameters, property names, types or enums.
This is accepted as a converter artifact, and `v3.16.json` is canonical. Any other difference means stop and ask.

## Sources consulted beyond the spec (approved 2026-10-04)

During the open-question review, the maintainer approved two secondary sources. Any further use needs approval again.

- **developer.konghq.com Admin API EE 3.16 reference.** It renders `Kong/developer.konghq.com: api-specs/gateway/admin-ee/3.16/openapi.yaml` (commit `3e1fb79`, "Release: Gateway 3.16", 2026-09-15). A deep comparison found it **identical** to `resources/kong-admin-api/v3.16.json`, so it adds no information.
- **Kong open-source code (`github.com/Kong/kong`)**, used for runtime behaviour in Q4–Q6. There is no open-source 3.16 tag; tag `3.9.3` and `master` (3.10.0) were read and agree on everything used here:
  - `kong/api/endpoints.lua`: pagination and `handle_error`;
  - `kong/db/errors.lua`: error codes and the error table;
  - `kong/tools/http.lua`: default error bodies.

  Enterprise-only resources (RBAC, workspace groups, licenses, event hooks, consumer groups, keyring) are not in that tree and remain spec-only.

## Decisions (reviewed with the maintainer, 2026-10-04)

| # | Issue | Decision |
|---|---|---|
| Q1 | `Route` is `oneOf [RouteJson, RouteExpression]` without a discriminator. | Two DTOs behind a `Route` interface. `RouteFactory` picks `RouteExpression` when `expression` is present and non-null. |
| Q2 | `/{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/endpoints/{workspace}{RBACRoleEndpointId}` has two adjacent parameters, and `RBACRoleEndpoint` has no `id` property (it is identified by `workspace` + `endpoint`). The template is malformed. | **Kept blocked** (list below): a path shown to be wrong is not sent. Supported routes are the global `rbacRoleEndpoints()` for single items, and `workspaceRbacRoles()->endpoints($role)->list()/create()`. Follow the spec once Kong fixes it. |
| Q3 | `/workspace_/groups…` uses a literal `workspace_` segment. | **Literal path, flagged.** `WorkspaceGroups::PATH = '/workspace_/groups'` drives both requests and attributes. The `$groups` parameter keeps the spec's name. There is an `@warning` on the class (not `@experimental`) and a README note. It is never workspace-prefixed. A future spec revision is a one-line change. |
| Q4 | Validation/conflict bodies beyond `{message, status}` are undefined in the spec. | From Kong core: database errors return `{code, name, message, fields}` (`options` for invalid options). `KongApiException` adds `errorCode`, `errorName` and `errorFields` (read leniently) and keeps the raw `details`. |
| Q5 | The end of pagination is unspecified. | Confirmed in Kong core: the last page omits `offset` and has `next: null`. `all()` stops when `offset` is absent or null, and keeps the repeated-offset guard. |
| Q6 | The `next` URI format is unspecified. | Kong core builds a root-relative path. Nested lists drop `tags` and `size`, and the open-source build adds no workspace prefix. `next` stays exposed but isn't followed. New `nextPage($page, $options)` on every paginated resource repeats `list()` with the page's `offset`. |
| Q7 | `/tags`, `/tags/{tag}`, `/admins` and `/event-hooks` return `next` but declare no paging parameters. | Spec-literal: `list()` takes no options, with no `all()` and no `nextPage()`. |
| Q8 | `GET /licenses` returns one license; `POST`/ping/test on event hooks return the list envelope; `GET /admins/{id}/workspaces` returns one `Workspace`. | Literal types, flagged in PHPDoc and in the README "Spec quirks" section. |
| Q9 | `list_consumers` on `GET /consumer_groups/{id}` has no modelled result. | Kept: send the flag and return `ConsumerGroupInsideWrapper`. Members come from `consumerGroups()->consumers($group)`. |
| Q10 | The rate-limiting override body uses literal dotted keys typed as strings, while the response nests integers. | Spec-literal input with the dotted keys, listed in README "Spec quirks". Arrays allow any other shape. |
| Q11 | `UpstreamIdForTarget` description copy slip. | Kept `$upstreamId`, following the parameter name. |
| Q12 | 403 isn't defined in the spec. | New `ForbiddenException` (extends `KongApiException`) for 403. |
| Q13 | Partial `config` is deeply specified. | Final for v1: a plain `array<array-key, mixed>`; top-level fields are typed. |
| Q14 | `Target` timestamps are `number`. | Kept `?float` per spec. |
| Q15 | Operations without a response schema. | Kept: actions return `void`; `Admins::roles()` returns the decoded object. |
| Q16 | Mutation threshold. | Kept at 80% (runs on `main` and weekly). |
| Q17 | Event-hook sources `data` is a dynamic map described by example. | Kept as a plain array. |
| Q18 | Secrets the spec doesn't mark `x-encrypted`. | A reviewed list in the generator (`models.SENSITIVE`) gives them `ENCRYPTED`/`__debugInfo()` redaction and `#[SensitiveParameter]`, pinned by `ModelRoundTripTest::reviewedSecrets()`. The list: KeyAuth `key`, Jwt `secret`, RbacUser `user_token`, admin registration and password-reset `password`/`token`, keyring `key` material, Vault-sync `token`, KeyringImportResult `password`, license report `license_key`, event-hook and webhook `secret`. |
| Q19 | RBAC users and roles exist globally and under `/{workspace}`. | Kept as two explicit families: `rbacUsers()`/`rbacRoles()` and `workspaceRbacUsers()`/`workspaceRbacRoles()`. |
| Q20 | `/keyring/recover` is multipart-only; `/config` accepts JSON, YAML or multipart. | `recover()` sends multipart. `/config` gets `apply(array)` (JSON) plus `applyYaml(string)` (`application/yaml`). |

## Blocked operations

`tests/Conformance/SpecCoverageTest.php` parses the lines between the markers below.
Format: `METHOD path` (the spec's path template, verbatim).

<!-- blocked:start -->
DELETE /{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/endpoints/{workspace}{RBACRoleEndpointId}
GET /{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/endpoints/{workspace}{RBACRoleEndpointId}
PATCH /{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/endpoints/{workspace}{RBACRoleEndpointId}
PUT /{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/endpoints/{workspace}{RBACRoleEndpointId}
<!-- blocked:end -->
