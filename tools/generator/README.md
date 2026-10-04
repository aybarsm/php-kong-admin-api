# Code generator

A spec-driven generator for the repetitive parts of this package: entity DTOs, CRUD resources, fixtures
and feature tests. It reads the canonical spec named by `KongSpec::SPEC_FILE`
(`resources/kong-admin-api/v3.16.json`), and writes PHP in the same style as the hand-written reference
classes (`Services`, `Service`, `ServiceInput`, `Routes`).

Generated files are ordinary source: they are committed, reviewed and covered by every gate. The generator
is a dev tool only. It is excluded from the Composer package (`.gitattributes`) and has no runtime role.

## Requirements
- Python 3.10+ (standard library only)
- `composer install` done (the generator runs `vendor/bin/php-cs-fixer` on what it writes)

## Usage
Run from the repo root:

```bash
python3 tools/generator/phase4a.py   # regenerate Phase 4a (core gateway entities)
composer ci && composer test:mutate  # then run every gate
```

Each phase script prints the files it wrote. The output is deterministic. On an unchanged spec, a
rerun produces no diff, and `git status` must stay clean.

## Layout
| File | Role |
|---|---|
| `models.py` | Spec loading (stops with the exact path if the spec is missing), type mapping, output/input DTO rendering, nested DTOs, `x-encrypted` handling, `write()`/`finalize()` |
| `resources.py` | CRUD(+PUT) resource classes from a config row. Operation IDs and `OperationScope` are read from the spec, never typed in |
| `fixtures.py` | `tests/Fixtures/*.json` with every non-`writeOnly` property; spec `example` values where given, deterministic placeholders otherwise |
| `feature_tests.py` | One Pest feature test per resource config |
| `phase4a.py` | Phase 4a tables (enum locations, inline-object names, resource configs) and the entry point |

## What comes from where
- **From the spec (never hand-entered):** property names, types, `required`, `nullable`, `writeOnly`,
  `x-foreign`, `x-encrypted`, enum values, descriptions, operationIds, and which paths have a
  `/{workspace}` twin.
- **From the phase tables (the spec doesn't determine these):** class names for inline objects
  (`NESTED`), which enum class each enum location uses (`ENUMS`), resource class/accessor names and
  path-parameter argument names (`CONFIGS`).

## Rules
- **Don't hand-edit generated files.** Change the generator or the phase tables, regenerate, and
  commit both. A hand edit is overwritten on the next run. If a class needs behaviour the generator
  can't express, remove it from the phase tables and maintain it by hand.
- Hand-written classes (`Service`, `ServiceInput`, `Route*`, `Services`, `Routes`, `Tags`, `KongClient`
  accessors, nested accessors on `Services`/`Routes`) are never written by the generator.
- Anything the spec leaves ambiguous goes to `docs/spec-notes.md` as an open question before it goes
  into a table.
- Every gate still applies to generated code: the conformance tests check it against the spec
  independently of the generator.

## Adding a phase
Copy `phase4a.py` to `phaseNx.py`, replace its tables, run it, review the diff, then run the gates. A
non-CRUD endpoint (e.g. the Information or Keyring operations) is hand-written instead; the resource
generator only covers the `list`/`all`/`get`/`create`/`update`/`upsert`/`delete` shape.

## Upgrading the Kong spec
After the upgrade diff is approved (skill Workflow B), bump `KongSpec::SPEC_FILE`, rerun every phase
script, and review the diff. Changed properties, enums and operationIds flow through automatically. New
inline objects or enum locations make the generator fail with a `KeyError` naming the
`(class, location)` that needs a table entry.
