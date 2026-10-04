# Spec notes: Kong Admin API v3.16 (`resources/kong-admin-api/v3.16.json`)

This is a register of anomalies, open questions and blocked operations in the spec.
Every entry here comes from the spec. None comes from web docs or memory of Kong.
Status is **open** until the maintainer decides. "Default" is how the code behaves meanwhile.

## JSON vs YAML (accepted 2026-10-04)

`v3.16.yaml` writes 48 large integers in scientific notation (e.g. `created_at: 1.627588552e+09`), so they parse as floats.
All values are numerically identical to `v3.16.json`, and none of the differences touch paths, methods, parameters, property names, types or enums.
This is accepted as a converter artifact, and `v3.16.json` is canonical. Any other difference means stop and ask.

## Open questions

| # | Issue | Default | Status |
|---|---|---|---|
| Q1 | `Route` is `oneOf [RouteJson, RouteExpression]` with no discriminator. | Two DTOs share a `Route` interface. A route is a `RouteExpression` iff `expression` is present and non-null (`Models\RouteFactory`). | **decided** 2026-10-04: default applied |
| Q2 | `/{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/endpoints/{workspace}{RBACRoleEndpointId}` has two adjacent parameters with no separator. | **Blocked** (see list below). | **deferred** to the very end of package development (decided 2026-10-04) |
| Q3 | `/workspace_/groups…` uses the literal segment `workspace_`. | Implement it literally. | open |
| Q4 | 400/409 error bodies for entity validation and uniqueness are undefined. Only `{message, status}` (`BaseError`) exists. | The exception carries the status and `message` when present. The raw decoded body is in `details` and is not spec-defined. | open |
| Q5 | The spec doesn't say whether `offset` is omitted or null on the last page. | `all()` stops when `offset` is absent or null, and throws if an offset repeats. | open |
| Q6 | The `next` URI format (absolute or relative) is unspecified. | Exposed on `Page`, never followed. | open |
| Q7 | `/tags`, `/tags/{tag}` and `/admins` return `next` but declare no `size`/`offset`/`tags` parameters. | `list()` takes no options. No `all()`. | open |
| Q8 | `GET /licenses` (a list) returns a single `LicenseResponse`. `POST /event-hooks` returns the list envelope. `GET /admins/{id}/workspaces` returns a single `Workspace`. | Implement the literal return types and flag them in PHPDoc. | open |
| Q9 | `GET /consumer_groups/{id}?list_consumers=true`: the wrapper only has `consumer_group`. | Return the wrapper DTO and send the flag. No extra field is modeled. | open |
| Q10 | `PUT /consumer_groups/{id}/overrides/plugins/rate-limiting-advanced` has no request body schema. | Send no body and return `array<string, mixed>`. | open |
| Q11 | `UpstreamIdForTarget`'s description says "ID or target of the Target to lookup". | Follow the parameter name: `$upstreamId`. | open |
| Q12 | 403 is never defined in the spec. | Maps to the base `KongApiException`. | open |
| Q13 | `Partial*.config` is fully specified and deeply nested. | Kept as a plain array (`array<array-key, mixed>`) in v1. Top-level fields are typed. | **accepted for now** (2026-10-04) |
| Q14 | `Target.created_at`/`updated_at` are `number`; everything else uses `integer`. | Follow the spec: `?float`. | open |
| Q15 | `POST /admins` (200), `register`, `password_resets`, and several Keyring/Debug operations have no response schema. | Methods return `void`. | open |
| Q16 | Neither php-pro source gives a mutation threshold. | `--min=80`. | open |

## Blocked operations

`tests/Conformance/SpecCoverageTest.php` parses the lines between the markers below.
Format: `METHOD path` (the spec's path template, verbatim).

<!-- blocked:start -->
DELETE /{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/endpoints/{workspace}{RBACRoleEndpointId}
GET /{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/endpoints/{workspace}{RBACRoleEndpointId}
PATCH /{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/endpoints/{workspace}{RBACRoleEndpointId}
PUT /{workspace}/rbac/roles/{RBACRoleIdForNestedEntities}/endpoints/{workspace}{RBACRoleEndpointId}
<!-- blocked:end -->
