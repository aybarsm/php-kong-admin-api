# Plugin doc notes

Typed plugin configurations come from `resources/kong-admin-api/plugins/{Category}/{plugin}.md` (supplied
2026-10-04). Each doc has YAML front matter plus one JSON Schema block. This file records doc anomalies,
blocked plugins, and the defaults applied while a question is open. Like `spec-notes.md`, nothing here
is resolved from memory or the web.

## Blocked plugins

These docs contain another plugin's schema (a copy error), so their own plugin can't be implemented from
them. `PluginConformanceTest` reads this list. A doc leaves it when a corrected doc replaces it.

<!-- blocked-plugins:start -->
<!-- blocked-plugins:end -->

None. The four copies found on 2026-10-05 (session, syslog, acl, proxy-cache) were replaced with their own docs the same day (P1).

## Decisions (reviewed with the maintainer, 2026-10-05)

| # | Issue (docs only) | Decision |
|---|---|---|
| P1 | `session.md`, `syslog.md` and `acl.md` contained the CORS doc, and `proxy-cache.md` the Access Control Enforcement doc. | **Docs replaced** by the maintainer with their own docs, and all four are implemented. The blocked mechanism stays for future copy errors: a duplicate that isn't listed stops the generator. |
| P2 | The wire `name` comes from the front-matter `url` slug, and two filenames differ from it: `access-control-enforcement.md` → `ace`, `response-rate-limiting.md` → `response-ratelimiting`. The OpenAPI spec lists no plugin names to cross-check against. | **`url` slug.** `NAME` is `ace` and `response-ratelimiting`. Namespaces and class names follow the filename (`AccessControlEnforcement`, `ResponseRateLimiting`). |
| P3 | The docs carry no Kong Gateway version (only `min_version.gateway`), and the directory isn't versioned like the spec. | **Assume `KongSpec::VERSION`** (3.16.0). On a spec upgrade, refreshed docs are requested and diffed (skill Workflow B step 6). |
| P4 | `x-referenceable` integer fields (`redis.port` in acme, basic-auth, ace, rate-limiting, response-ratelimiting) accept a `{vault://…}` reference string in Kong, but the doc types them `integer`. | **`int`, as the doc says.** A vault reference is sent with the array form of `config`. |
| P5 | Root `protocols` enums are per-plugin subsets of the spec `Protocol` values. | **Global `list<Enums\Protocol>`.** The conformance test checks the subset, and Kong rejects a protocol the plugin doesn't allow. |
| P6 | Many counts and durations are `number` (e.g. rate-limiting `minute`, `second`, `error_code`), not `integer`. | **`int\|float`** in plugin classes (`Data::number()`/`numberOrNull()`), so JSON integers stay int. Spec models keep `float` for `number` (spec-notes Q14). |
| P7 | `config.llm.metadata` (ai-request-transformer, ai-response-transformer) is `nullable` with `x-speakeasy-type-override: any`. | **Free-form** `array<array-key, mixed>`. |
| P8 | Scope fields a doc omits (e.g. acme has no `service`/`route`) can't be set on `{Plugin}Input`, but nested plugin resources (`services()->plugins($id)`) still set the scope through the path. | **Constructor only.** There's no guard in the nested resources, and Kong rejects the scope at runtime. |
| P9 | Names derived as `{Plugin}` + the config path were up to 83 characters long. | **No plugin prefix** for nested DTOs and enums: the PascalCase config path (`RateLimiting\RedisCloudAuthentication`, `RateLimiting\Policy`). A name that is a PHP reserved word or clashes with a class the generated files import (`Model`, `Input`, `Data`, `Protocol`, …) falls back to `{Plugin}` + the path (today only `AiProxy\AiProxyModel`). The `NAMES` table overrides any name. |
| P10 | Namespaces use the doc directory names verbatim, so the category is `AI`, not `Ai` as the acronyms-as-words rule would give. | **`AI`, verbatim** (`Plugins\AI\AiProxy`). |
