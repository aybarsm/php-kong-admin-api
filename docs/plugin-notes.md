# Plugin doc notes

Typed plugin configurations come from `resources/kong-admin-api/plugins/{Category}/{plugin}.md` (supplied
2026-10-04). Each doc has YAML front matter plus one JSON Schema block. This file records doc anomalies,
blocked plugins, and the defaults applied while a question is open. Like `spec-notes.md`, nothing here
is resolved from memory or the web.

## Blocked plugins

These docs contain another plugin's schema (a copy error), so their own plugin can't be implemented from
them. `PluginConformanceTest` reads this list. A doc leaves it when a corrected doc replaces it.

<!-- blocked-plugins:start -->
- `Authentication/session.md`: front matter, heading and schema are the CORS doc (`url: /plugins/cors/reference/`; schema identical to `Security/cors.md`).
- `Logging/syslog.md`: front matter, heading and schema are the CORS doc (`url: /plugins/cors/reference/`; schema identical to `Security/cors.md`).
- `TrafficControl/acl.md`: front matter, heading and schema are the CORS doc (`url: /plugins/cors/reference/`; schema identical to `Security/cors.md`).
- `TrafficControl/proxy-cache.md`: front matter, heading and schema are the Access Control Enforcement doc (`url: /plugins/ace/reference/`; schema identical to `TrafficControl/access-control-enforcement.md`).
<!-- blocked-plugins:end -->

## Open questions (defaults applied; to review)

| # | Issue (docs only) | Default handling |
|---|---|---|
| P1 | Four docs are copies of another plugin's doc (list above). | Blocked; not implemented. Needs corrected docs for `session`, `syslog`, `acl` and `proxy-cache`. |
| P2 | The wire `name` comes from the front-matter `url` slug, and two filenames differ from it: `access-control-enforcement.md` → `ace`, `response-rate-limiting.md` → `response-ratelimiting`. The OpenAPI spec lists no plugin names to cross-check against. | `NAME` = `url` slug (`ace`, `response-ratelimiting`). Namespaces and class names follow the filename (`AccessControlEnforcement`, `ResponseRateLimiting`). |
| P3 | The docs carry no Kong Gateway version (only `min_version.gateway`), and the directory isn't versioned like the spec. | Treated as matching `KongSpec::VERSION` (3.16.0). On a spec upgrade, refreshed docs are requested and diffed (skill Workflow B step 6). |
| P4 | `x-referenceable` integer fields (`redis.port` in acme, basic-auth, ace, rate-limiting, response-ratelimiting) accept a `{vault://…}` reference string in Kong, but the doc types them `integer`. | Typed `int` as the doc says. A vault reference is sent with the array form of `config`. |
| P5 | Root `protocols` enums are per-plugin subsets of the spec `Protocol` values. | Typed `list<Enums\Protocol>`. The conformance test checks the subset; Kong rejects a protocol the plugin doesn't allow. |
| P6 | Many counters and durations are `number` (e.g. rate-limiting `minute`, `second`), not `integer`. | `float`, as the doc says (same rule as spec-notes Q14). PHP widens an `int` argument to `float`. |
| P7 | `config.llm.metadata` (ai-request-transformer, ai-response-transformer) is `nullable` with `x-speakeasy-type-override: any`. | A free-form `array<array-key, mixed>`. |
| P8 | Scope fields a doc omits (e.g. acme has no `service`/`route`; basic-auth has no `consumer`) can't be set on `{Plugin}Input`. Nested plugin resources (`services()->plugins($id)`) can't stop a typed input for a plugin that doesn't allow that scope. | Restricted by the typed constructor only. Kong rejects the scope at runtime. |
| P9 | Nested class names are derived from the config path and can be long (up to 83 characters, e.g. `AccessControlEnforcementRateLimitingRedisCloudAuthenticationOauthClientSecretJwtAlg`). | Deterministic `{Plugin}{Path}` names, with `NAMES` overrides available in `tools/generator/plugins.py`. |
| P10 | Namespaces use the doc directory names verbatim, so the category is `AI`, not `Ai` as the acronyms-as-words rule would give. | Directory name verbatim (`Plugins\AI\AiProxy`), as requested. |
