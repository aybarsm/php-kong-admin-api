# CLAUDE.md: aybarsm/kong-admin-api

Framework-agnostic, strictly typed PHP ^8.3 client for the Kong Gateway Admin API.
Root namespace `Aybarsm\Kong\AdminApi\` → `src/`. Built against Kong Gateway **3.16.0**
(`KongSpec::VERSION`) from `resources/kong-admin-api/v3.16.json`.

**Before adding, changing or upgrading any Kong resource, load the `kong-php-client` skill.**

## Commands
- Install: `composer install`
- Tests: `composer test` (Pest 4) · coverage gate: `composer test:coverage` (≥90%; needs pcov or xdebug)
- Mutation: `composer test:mutate` (≥80%, parallel)
- Static analysis: `composer analyse` (PHPStan level 9 + 100% type coverage via `tomasvotruba/type-coverage`,
  src + tests, no baseline)
- Code style: `composer cs` (check) · `composer cs:fix` (apply)
- Everything CI runs: `composer ci && composer test:mutate` (mutation runs in `.github/workflows/mutation.yml`
  on pushes to `main`, weekly and on demand, not on pull requests; run it locally before pushing)
- One file: `vendor/bin/pest tests/Feature/Resources/ServicesTest.php`
- Regenerate generated code: `python3 tools/generator/phase4a.py`, `… phase4b.py`, `… phase4c.py`, `… phase4d.py` (one script per phase,
  run in order; see `tools/generator/README.md`). A rerun on an unchanged spec must leave `git status` clean.

## Kong API source of truth (strict)
- The Kong Admin API specification lives only in `resources/kong-admin-api/{version}.yaml` or `resources/kong-admin-api/{version}.json`, where `{version}` is the Kong Gateway version. Both formats may be present for the same version; use whichever is more convenient to read and query. If both exist and disagree, stop and tell me.
- This spec is the only authority for endpoints, methods, parameters, schemas and error shapes. Do not use web documentation, other versions' specs or your own memory of Kong to fill gaps. If the spec is silent on something, list it as an open question.
- If the directory is missing, empty, or has no spec for the version being worked on, stop immediately and report an error that names the exact path(s) you expected. Do not continue with a partial plan.
- If several versions are present, ask me which one to target; default to the highest.
- `resources/` at the repo root holds specs. Endpoint classes live under `src/Resources/` (`Kong\Resources`). Keep the two distinct.

Project specifics (decided 2026-10-04):
- `{version}` is `v` + major.minor, e.g. `v3.16`. The full gateway version is the spec's `info.version`.
- `Kong\…` in the rules above means `Aybarsm\Kong\AdminApi\…` in this repo.
- `v3.16.json` is canonical. `v3.16.yaml` writes 48 large integers in scientific notation
  (they parse as floats, with values identical to the JSON). This was reviewed and accepted as an
  encoding artifact. Any **other** JSON/YAML difference means stop and ask.
- Spec anomalies, open questions and blocked operations live in `docs/spec-notes.md`. Never resolve
  one from memory; ask.

## Code generation
- Uniform CRUD entities (DTOs, resources, fixtures, feature tests) come from `tools/generator/`, which reads
  the spec named by `KongSpec::SPEC_FILE`. Hand-written: `Service*`, `Route*`, `Services`, `Routes`,
  `Tags`, `KongClient`, enums, and any non-CRUD endpoint (e.g. the Consumer Group membership and
  override resources). The README lists them. Generated code passes the same gates as hand-written code.

## Architecture rules
- `KongClient` is only a factory: `$client->services()->get($id)`, `$client->inWorkspace('x')->plugins()`.
  It never makes API calls itself.
- HTTP lives only in `Internal\Transport`. It type-hints PSR-18 `ClientInterface` and the PSR-17
  request and stream factories, with Guzzle as the default. Resources never touch PSR-7/18 or Guzzle.
- Never return or accept `ResponseInterface`, and never expose a Guzzle type, in any public signature.
  The one exception is the `KongClient` constructor, which takes only PSR interfaces.
- Responses map to `final readonly` DTOs via `fromArray()`/`toArray()`, with properties taken from the
  spec schema (`#[Schema('Name')]`). Write methods accept `XInput|array<string, mixed>`.
  `XInput::toArray()` omits nulls.
- Resource methods: `list()` → `Page<T>`, `all()` → lazy `Generator<int, T>` (follows `offset`),
  `get()`, `create()` (POST), `update()` (PATCH), `upsert()` (PUT, only if the spec has it), `delete()`.
  Provide only the methods the spec defines for that path. Every public method carries
  `#[Operation(method, specPath, operationId, scope)]`.
- Nested endpoints mirror the spec paths: `$client->services()->routes($svc)->list()`.
- Workspace: `/{workspace}` is prefixed only on operations that have the twin in the spec (`OperationScope`).
- Errors: map by status in Transport, and also catch PSR-18 `ClientExceptionInterface` and Guzzle
  `RequestException`. Everything surfaces as `KongApiException` (400 Validation, 401 Unauthorized,
  404 NotFound, 409 Conflict, 5xx Server, no response → Transport, bad payload → UnexpectedResponse).
  Caller errors throw `Exceptions\InvalidArgumentException`.
- PHP 8.3: `readonly class` for every DTO, typed class constants, `#[\Override]` on implementations,
  union/intersection/DNF types, `match`, constructor promotion, backed enums for spec enums.
- `declare(strict_types=1);` in every file. PSR-12, PSR-4. PHPStan level 9 with every array shape
  documented (`list<T>`, `array<string, mixed>`, `array{…}`). No native `mixed` in `src/`.

## Naming
- Resources: plural PascalCase of the spec path or tag, acronyms as words: `Services`, `CaCertificates`,
  `HmacAuths`, `RbacUsers`, `OidcJwks`. Nested ones go in `Resources\Nested\{Parent}{Child}` (`ServiceRoutes`).
- Models: singular spec schema name in PascalCase (`Service`, `Sni`, `RbacRole`); inputs `ServiceInput`;
  nested objects named for their role (`UpstreamHealthchecks`); shared objects in `Models\Shared`.
- Enums: `Enums\{Concept}`, with case names in PascalCase and values exactly as in the spec.
- Path-parameter arguments: `$idOrName`, `$id`, `$idOrUsername` and so on, following the spec parameter's semantics.
- Tests: `tests/Feature/Resources/{Resource}Test.php` (nested: `…/Nested/`), fixtures
  `tests/Fixtures/{snake_case_class}.json` (`CaCertificate` → `ca_certificate.json`) with every spec property.

## Definition of done
- Every public method traces to a spec path (conformance tests green) and has a Pest test asserting
  method, path, query and body against `MockHandler` + history. No network.
- `composer ci` and `composer test:mutate` pass. There is no baseline and no new ignores.
- The DTO, enum and model conformance tests match the spec. New anomalies are recorded in `docs/spec-notes.md`.
- Public classes and methods have docblocks, including `@throws` and the spec operationId.
- README is updated if public API changed, and every README code example is mirrored in
  `tests/Feature/ReadmeExamplesTest.php`.
- Generated code changed only through `tools/generator/` (generator or phase tables), regenerated and
  committed together with the generator change.

## Never
- Never use web docs, other spec versions or memory of Kong. Never guess a missing spec; stop and name the path.
- Never edit files under `resources/`.
- Never hand-edit a generated file (listed by the phase scripts in `tools/generator/`); change the generator.
- Never return `ResponseInterface`, never leak Guzzle types, and never make HTTP calls from a resource or from `KongClient`.
- Never add a PHPStan baseline, `@phpstan-ignore` or `ignoreErrors` without explicit approval.
- Never use dynamic properties, `mixed` native types, `var_dump`/`dd`/`print_r`/`echo`, or static mutable state.
- Never put a token or `x-encrypted` value in an exception message, log or `__debugInfo`.
- Never add framework dependencies (Laravel, Symfony, …) or async runtimes.
