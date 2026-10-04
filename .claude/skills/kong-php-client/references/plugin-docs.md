# Plugin docs and typed plugin classes

## Where the docs are
`resources/kong-admin-api/plugins/{Category}/{plugin}.md`. Category directories are `AI`, `Authentication`,
`Logging`, `Monitoring`, `Security`, `TrafficControl` and `Transformation`. They map verbatim to
`Aybarsm\Kong\AdminApi\Plugins\{Category}`. Never edit a doc.

A doc has three parts:
- **Front matter:** `title`, `description`, `url: "/plugins/{name}/reference/"` (the wire `name`),
  `min_version.gateway`, `tags`, …
- **A heading:** `# {Title} Plugin Configuration Reference`.
- **Exactly one fenced `json` block:** a JSON Schema whose root `properties` are `config` plus the scope fields
  the plugin accepts (`consumer`, `consumer_group`, `route`, `service`, `protocols`, `expressions`). The root
  may also carry `required` (usually `["config"]`) and `x-supported-partials`.

## Recipes
```bash
D=resources/kong-admin-api/plugins/TrafficControl/rate-limiting.md
test -s "$D" || { echo "Missing plugin doc: $D"; exit 1; }
schema() { sed -n '/^```json$/,/^```$/p' "$1" | sed '1d;$d'; }

schema "$D" | jq '.properties | keys'                         # scope fields the plugin accepts
schema "$D" | jq '.properties.config.properties | keys'       # config fields
schema "$D" | jq '.properties.protocols.items.enum'           # allowed protocols
schema "$D" | jq '[paths(type=="object" and has("enum"))] | map(map(tostring) | join("."))'
schema "$D" | jq '[paths(type=="object" and ."x-encrypted" == true)] | map(map(tostring) | join("."))'
grep -m1 '^url:' "$D"                                          # wire name = the slug

# Copy-error check: identical config schemas across docs
for f in resources/kong-admin-api/plugins/*/*.md; do
  printf '%s %s\n' "$(schema "$f" | jq -S -c .properties.config | md5)" "$f"; done | sort | uniq -w32 -D
```

## Keyword → PHP mapping (what `tools/generator/plugins.py` understands)
| Doc schema | PHP |
|---|---|
| `string` / `integer` / `number` / `boolean` | `string` / `int` / `float` / `bool` (`number` is always `float`, even for counts) |
| `enum` (string or integer) | a backed enum in the plugin namespace; root `protocols` uses the global `Enums\Protocol` |
| `array` of scalars / enums / objects | `list<string>`, `list<int>`, `list<Enum>`, `list<Nested>` |
| `object` with `properties` | nested `final readonly` DTO |
| `object` with only `id` (incl. `x-foreign`) | `Models\Shared\ForeignKey` (`ForeignKey|string` on inputs) |
| `additionalProperties` string / string list / object | `array<array-key, string>`, `array<array-key, list<string>>`, `array<array-key, Nested>` |
| `object` without `properties` (`additionalProperties: true`, `x-speakeasy-type-override`) | `array<array-key, mixed>` |
| `required` | non-nullable on the output DTO; still optional on inputs (PATCH) with "Required by the docs" |
| `default` | not applied; shown as `Default: …` in the parameter docblock |
| `x-encrypted` | `ENCRYPTED` + `__debugInfo()` redaction + `#[SensitiveParameter]` on inputs |
| `x-referenceable` | no type change: pass an array to send a `{vault://…}` reference to a non-string field (P4) |
| `minimum`/`maximum`/`minLength`/`maxLength` | not validated client-side; Kong validates |
| `x-supported-partials` | named in the `{Plugin}Input` docblock; link Partials through `partials` |

## Classes per plugin (namespace `Plugins\{Category}\{Plugin}`)
| Class | Role | Contract |
|---|---|---|
| `{Plugin}Input` | Full plugin request body: `config`, the scope fields the doc lists, and the generic spec `Plugin` fields (`enabled`, `instance_name`, `tags`, `ordering`, `partials`, …). Sends `name` itself. | `TypedPluginInput` |
| `{Plugin}Config` | Typed `config` from a response: `{Plugin}Config::fromPlugin($plugin)` or `PluginRegistry::config($plugin)` | `PluginConfig` (extends `Model`) |
| `{Plugin}ConfigInput` | Typed `config` for a request; every field optional, nulls omitted | `Input` |
| `{Plugin}{Path}` | Nested config objects (output DTOs, reused by the inputs) | `Model` |
| `{Plugin}{Path}` enum | Enum at a config path; values verbatim from the doc | backed enum |

Every class carries `#[PluginSchema('{Category}/{plugin}.md', '<JSON pointer into the doc schema>')]`.
`{Plugin}Input`, `{Plugin}Config` and `{Plugin}ConfigInput` declare `public const string NAME` (the wire name).

```php
$plugin = $client->services()->plugins('billing')->create(new RateLimitingInput(
    config: new RateLimitingConfigInput(minute: 20.0, policy: RateLimitingPolicy::Local),
    tags: ['edge'],
));
$config = RateLimitingConfig::fromPlugin($plugin);       // throws InvalidArgumentException for another plugin
$any = PluginRegistry::config($plugin);                   // PluginConfig, or null for an undocumented plugin
```

## Tests
`tests/Conformance/PluginConformanceTest.php` reads every doc and every generated class. It checks:
- Coverage: every doc is implemented or blocked, and no class exists without a doc.
- `NAME` is the `url` slug, and `PluginRegistry::configs()` lists exactly the implemented plugins.
- DTO parameters equal the doc properties, with `required` mapped to non-nullable.
- Enums equal the doc `enum`, and the root `protocols` are a subset of `Enums\Protocol`.
- Fixtures round-trip, and the inputs serialise exactly like the outputs.
- `{Plugin}Input` fields equal the doc scope fields plus the spec `Plugin` fields.
- `x-encrypted` values are redacted.

Fixtures are generated (`tests/Fixtures/Plugins/{Category}/{plugin}.json`) with every config property.
