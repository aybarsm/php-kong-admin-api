---
name: kong-php-client
description: Use when implementing, changing, extending or upgrading any Kong Admin API resource, endpoint, DTO, enum or test in aybarsm/kong-admin-api — e.g. "add the Upstreams resource", "support PUT on /vaults", "fix the Plugin model", "add a field to Service", "upgrade to Kong 3.17", "diff the new Kong spec". Covers the spec-first workflow, the merged php-pro coding rules, and the add-resource and upgrade-version procedures.
---

# Kong PHP client: implementation and upgrade skill

The spec at `resources/kong-admin-api/{version}.json` (canonical) is the only authority. Read CLAUDE.md
first: its source-of-truth rules override anything here.
References: `references/spec-reading.md` (jq recipes), `references/resource-template.md`,
`references/dto-template.md`, `references/test-template.md`.

## Merged PHP rules (VoltAgent php-pro + Jeffallan php-pro, framework parts removed; stricter rule wins)

**Typing**
1. `declare(strict_types=1);` is the first statement of every PHP file in src/ and tests/.
2. Native types on every property, parameter and return. No native `mixed` in src/; `mixed` may appear
   only as the value type in PHPDoc `array<string, mixed>` for spec free-form objects. (Jeffallan's
   MUST-NOT is stricter than VoltAgent's "avoid".)
3. Every array or iterable has a PHPDoc shape or generic: `list<T>`, `array<string, mixed>`, `array{…}`,
   `Generator<int, T>`, `@template` for generic containers (`Page<T>`).
4. Use `never` for functions that always throw and `void` for those that return nothing.

**Standards**
5. PSR-12 (enforced by PHP-CS-Fixer), PSR-4, and PSR-7/17/18 for HTTP. Composer dependencies are audited in CI.

**Modern PHP (8.3 floor)**
6. `final readonly class` for DTOs, inputs, value objects, config and attributes. Every class is `final`
   unless it's an abstract base or the exception base (Jeffallan's convention, adopted as a rule).
7. Constructor promotion; named arguments when calling DTO constructors; `match` instead of `switch`;
   first-class callables (`array_map(Service::fromArray(...), $rows)`); backed enums for every spec enum.
8. Typed class constants. `#[\Override]` on every interface implementation and parent override.
   `#[\SensitiveParameter]` on tokens and secrets.
9. Union, intersection and DNF types only where they express a real contract. Dynamic properties are
   banned (VoltAgent lists them as a feature, but they are deprecated as of 8.2).

**Static analysis**
10. PHPStan level 9 on src/ and tests/, with strict-rules, deprecation-rules and `reportUnmatchedIgnoredErrors`.
    No baseline, no ignores without approval. Type coverage is 100% for params, returns and properties.
    (Both sources say level 9; Jeffallan's extras are stricter and adopted. Psalm is not used.)

**Testing**
11. Pest 4. Every public method has a test, and every bug fix starts with a failing test.
12. Coverage ≥ 90% lines (stricter than VoltAgent's ">80%" and Jeffallan's "80%+"). Mutation testing
    via `pest --mutate` (VoltAgent; Jeffallan is silent).
13. HTTP is tested only through Guzzle `MockHandler` + `Middleware::history()`, never the network.
    Tests are typed and avoid `$this`: use the typed `Tests\Support\{MockKong, Fixture, Spec}` classes so PHPStan
    level 9 stays clean.
14. Use datasets (`->with()`) for status codes, enum cases and boundaries. Exception tests assert the
    class **and** the message or status.

**Docs, errors, security**
15. Every public class and method has a docblock (VoltAgent, stricter), including `@throws` and the spec
    operationId.
16. The public API throws only package exceptions. PSR-18 and Guzzle exceptions are wrapped, with `previous` set.
17. Secrets (token, `x-encrypted` fields) never appear in messages or `__debugInfo`. No
    `var_dump`/`dd`/`print_r`/`echo` in src/.
18. Inject dependencies through the constructor. No globals, singletons or static mutable state. All
    configuration goes through `ClientConfig`.

## Workflow A: adding (or changing) a resource

a. **Find the paths and schemas in the spec** (recipes in `references/spec-reading.md`):
   - Confirm the spec file exists. If it doesn't, stop and name the path.
   - List every (method, path) for the entity, including nested paths and the `/{workspace}` twin.
     Record which methods actually exist; don't assume CRUD.
   - Resolve the request and response schemas (`X`, `XWithoutParents`), path parameters (id vs
     id-or-name), query parameters, enums, `required`, `nullable`, `writeOnly`, `x-foreign` and `x-encrypted`.
   - Anything ambiguous goes into `docs/spec-notes.md` as an open question. Ask before guessing.

b. **Create the endpoint class** `Aybarsm\Kong\AdminApi\Resources\{Plural}` (or
   `Resources\Nested\{Parent}{Child}`) from `references/resource-template.md`, and add the accessor to
   `KongClient` (or to the parent resource for nested ones).

c. **Create the DTOs** `Models\{Schema}` (output) and `Models\{Schema}Input`, plus nested and shared objects and
   enums, from `references/dto-template.md`. Properties must match the spec schema exactly
   (ModelConformanceTest enforces this).

d. **Implement the methods against the spec**: exactly the methods present, each with
   `#[Operation(...)]` (one per spec path it serves) and the correct `OperationScope`. Paginated lists
   get both `list()` and `all()`.

e. **Test** with `references/test-template.md`: one Pest file per resource class.
   - For every method, assert the HTTP method, path (global **and** workspace-scoped when the scope is
     `Both`), query string and JSON body, and the mapped DTO values.
   - Fixtures are shaped like the spec schemas (`tests/Fixtures/`, `Fixture::specExample()`).
   - Include the error mapping for at least 404, and 401 where the spec declares it.
   - No network calls.

f. **Gates**: `composer ci && composer test:mutate`. Conformance tests must be green.

## Workflow B: upgrading to a new Kong version

a. **Check the spec exists**: `resources/kong-admin-api/v{newVersion}.json` or `.yaml`, for example
   `v3.17.json`. If neither exists, **stop** and return an error naming both expected paths. Don't
   fetch it, don't guess it, and don't proceed from the old spec. If both exist, deep-compare them.
   If they differ in anything other than the integer-encoding artifact that was already accepted,
   stop and report.

b. **Diff against the currently targeted spec** (`KongSpec::SPEC_FILE`) using the inventory recipes in
   `references/spec-reading.md`. Cover:
   - operations (method + path + operationId),
   - parameters (name, in, required, schema, bounds),
   - request and response schemas per operation,
   - every component schema (properties, types, `required`, `nullable`, `writeOnly`, `x-foreign`, `x-encrypted`),
   - every enum location,
   - security schemes, servers, and the pagination and error components.

c. **Report before editing anything**. Produce added, changed and removed tables, and flag
   **breaking** changes, e.g.:
   - removed operations or properties,
   - a property becoming required or changing type,
   - enum values removed,
   - parameter renames, lookup semantics changes (id-or-name → id), or scope changes.

   Mark which items are breaking for this library's public API and propose the semver bump. Wait for
   approval.

d. **Apply**:
   1. Update resources, DTOs, enums and tests.
   2. Update `docs/spec-notes.md`: re-check every open question against the new spec.
   3. Bump `KongSpec::VERSION` (from `info.version`) and `KongSpec::SPEC_FILE`.
   4. Update CLAUDE.md, README and composer.json description references.
   5. Run all gates.

   Conformance tests now read the new spec and must pass with no blocked operations beyond those you
   approve.
