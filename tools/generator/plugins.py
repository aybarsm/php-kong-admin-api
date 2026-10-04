"""Plugins: typed plugin configurations from the plugin docs (see tools/generator/README.md).

Reads every `resources/kong-admin-api/plugins/{Category}/{plugin}.md` (front matter + one JSON Schema block),
skips the docs listed as blocked in `docs/plugin-notes.md`, and writes per plugin, into
`src/Plugins/{Category}/{Plugin}/`:
  - `{Plugin}Input`        the whole plugin request body (TypedPluginInput; sends `name` itself)
  - `{Plugin}Config`       the typed `config` of a returned Plugin (PluginConfig::fromPlugin)
  - `{Plugin}ConfigInput`  the typed `config` of a request
  - nested DTOs and backed enums of the config, named `{Plugin}` + PascalCase config path
plus `src/Plugins/PluginRegistry.php`, `tests/Fixtures/Plugins/{Category}/{plugin}.json` and the plugin table
in README.md (between the `plugins:start`/`plugins:end` markers).

Every type decision comes from the doc. The generic Plugin fields of `{Plugin}Input` (`enabled`, `tags`, …)
come from the OpenAPI spec's `Plugin` schema, so they match `Models\\PluginInput` exactly.

Run from the repo root:  python3 tools/generator/plugins.py
"""
import json
import re
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent))

from fixtures import obj as fixture_object  # noqa: E402
from models import NS, ROOT, SPEC, Prop, finalize, first_sentence, render, write  # noqa: E402

DOCS = Path(ROOT, 'resources', 'kong-admin-api', 'plugins')
NOTES = Path(ROOT, 'docs', 'plugin-notes.md')
README = Path(ROOT, 'README.md')

# (doc slug, config location) -> class name, for nested DTOs or enums whose derived name should be shortened.
# Locations are dotted config paths without the `config.` prefix, with `[]` for array items and `{}` for map values,
# e.g. ('rate-limiting', 'redis.cloud_authentication'). Empty: every name is derived.
NAMES = {}

# Root properties a plugin doc may declare; any other root property stops the generator.
ROOT_FIELDS = ('config', 'consumer', 'consumer_group', 'expressions', 'protocols', 'route', 'service')

# JSON Schema keywords the generator understands (see the skill's references/plugin-docs.md).
KEYWORDS = {
    'type', 'properties', 'items', 'additionalProperties', 'description', 'default', 'enum', 'required',
    'minimum', 'maximum', 'minLength', 'maxLength', 'nullable', 'x-encrypted', 'x-referenceable', 'x-foreign',
    'x-lua-required', 'x-speakeasy-type-override',
}
ROOT_KEYWORDS = {'properties', 'required', 'x-supported-partials'}

PLUGIN_SCHEMA = NS + '\\Attributes\\PluginSchema'
ENCRYPTED_NOTE = 'Properties the plugin doc marks `x-encrypted`; redacted in __debugInfo().'
REQUIRED_NOTE = 'Required by the plugin doc.'

# The spec's generic Plugin schema: nested class / enum names as registered by phase4a.
SPEC_PLUGIN = SPEC['components']['schemas']['Plugin']
SPEC_NESTED = {('Plugin', 'ordering'): 'PluginOrdering', ('Plugin', 'partials[]'): 'PluginPartial'}
SPEC_ENUMS = {('Plugin', 'protocols[]'): 'Protocol'}


def pascal(name):
    """`ai-proxy` -> `AiProxy`, `cloud_authentication` -> `CloudAuthentication`."""
    return ''.join(p[:1].upper() + p[1:] for p in re.split(r'[^A-Za-z0-9]+', name) if p)


def case_name(value):
    """Enum case name for a doc value: `llm/v1/chat` -> `LlmV1Chat`, `HS256` -> `Hs256`, 8 -> `Value8`, -1 -> `ValueMinus1`."""
    if isinstance(value, bool) or not isinstance(value, (int, str)):
        raise ValueError(f'unsupported enum value {value!r}')
    if isinstance(value, int):
        return 'Value' + ('Minus' if value < 0 else '') + str(abs(value))
    parts = [p for p in re.split(r'[^A-Za-z0-9]+', value) if p]
    name = ''.join((p.lower() if p.isupper() else p)[:1].upper() + (p.lower() if p.isupper() else p)[1:] for p in parts)
    if not name:
        raise ValueError(f'enum value {value!r} has no usable characters')
    return 'Value' + name if name[0].isdigit() else name


def escape(pointer_segment):
    return pointer_segment.replace('~', '~0').replace('/', '~1')


def php_string(value):
    return "'" + value.replace('\\', '\\\\').replace("'", "\\'") + "'"


class Doc:
    """One plugin doc: identity from the front matter, schema from the single JSON block."""

    def __init__(self, path):
        self.path = path
        self.rel = path.relative_to(DOCS).as_posix()
        self.category = path.parent.name
        self.slug = path.stem
        text = path.read_text()
        match = re.match(r'^---\n(.*?)\n---\n', text, re.S)
        if match is None:
            raise SystemExit(f'{path}: no YAML front matter')
        front = match.group(1)
        self.title = self._front(front, 'title')
        url = re.search(r'^url: "?/plugins/([^/"]+)/reference/"?\s*$', front, re.M)
        if url is None:
            raise SystemExit(f'{path}: front-matter url is not /plugins/{{name}}/reference/')
        self.name = url.group(1)
        self.description = ' '.join(re.search(r'^description: (.*?)\n(?=\S)', front + '\n', re.S | re.M).group(1).split())
        version = re.search(r"^min_version:\n\s+gateway: '?([0-9.]+)'?", front, re.M)
        self.min_version = version.group(1) if version else None
        heading = re.search(r'^# (.+)$', text[match.end():], re.M)
        self.heading = heading.group(1) if heading else None
        blocks = re.findall(r'^```json\n(.*?)\n^```\s*$', text, re.S | re.M)
        if len(blocks) != 1:
            raise SystemExit(f'{path}: expected exactly one ```json block, found {len(blocks)}')
        self.schema = json.loads(blocks[0])
        self.plugin = pascal(self.slug)
        self.namespace = f'{NS}\\Plugins\\{self.category}\\{self.plugin}'
        self.directory = f'src/Plugins/{self.category}/{self.plugin}'

    @staticmethod
    def _front(front, key):
        found = re.search(rf'^{key}: "?(.*?)"?\s*$', front, re.M)
        if found is None:
            raise SystemExit(f'front matter has no {key}')
        return found.group(1)

    def attribute(self, pointer):
        return PLUGIN_SCHEMA, f"#[PluginSchema({php_string(self.rel)}, {php_string(pointer)})]"

    def validate(self):
        root = self.schema
        unknown = set(root) - ROOT_KEYWORDS
        if unknown:
            raise SystemExit(f'{self.rel}: unsupported root keywords {sorted(unknown)}')
        extra = set(root['properties']) - set(ROOT_FIELDS)
        if extra or 'config' not in root['properties']:
            raise SystemExit(f'{self.rel}: root properties must be config plus {ROOT_FIELDS[1:]}, got {sorted(root["properties"])}')
        if self.heading != self.title:
            raise SystemExit(f'{self.rel}: heading {self.heading!r} differs from title {self.title!r}; block it in {NOTES.name}?')

        def check(node, location):
            unknown = set(node) - KEYWORDS
            if unknown:
                raise SystemExit(f'{self.rel}: unsupported keywords {sorted(unknown)} at {location}')
            for name, child in node.get('properties', {}).items():
                check(child, f'{location}.{name}')
            if isinstance(node.get('items'), dict):
                check(node['items'], location + '[]')
            if isinstance(node.get('additionalProperties'), dict):
                check(node['additionalProperties'], location + '{}')

        check(root['properties']['config'], 'config')


def blocked():
    """Docs listed between the blocked-plugins markers of docs/plugin-notes.md."""
    text = NOTES.read_text()
    section = re.search(r'<!-- blocked-plugins:start -->(.*?)<!-- blocked-plugins:end -->', text, re.S)
    if section is None:
        raise SystemExit(f'{NOTES}: blocked-plugins markers missing')
    return set(re.findall(r'^- `([^`]+\.md)`', section.group(1), re.M))


def load_docs():
    if not DOCS.is_dir():
        raise SystemExit(f'Missing plugin docs directory: {DOCS}')
    paths = sorted(DOCS.glob('*/*.md'))
    if not paths:
        raise SystemExit(f'No plugin docs found: {DOCS}/{{Category}}/{{plugin}}.md')
    skip = blocked()
    known = {p.relative_to(DOCS).as_posix() for p in paths}
    if skip - known:
        raise SystemExit(f'{NOTES.name} blocks docs that do not exist: {sorted(skip - known)}')
    docs = [Doc(p) for p in paths if p.relative_to(DOCS).as_posix() not in skip]
    seen = {}
    for doc in docs:
        doc.validate()
        key = json.dumps(doc.schema['properties']['config'], sort_keys=True)
        if key in seen:
            raise SystemExit(f'{doc.rel} has the same config schema as {seen[key]}: a copy error? Block it in {NOTES.name}.')
        seen[key] = doc.rel
        for other in docs:
            if other is not doc and other.name == doc.name:
                raise SystemExit(f'{doc.rel} and {other.rel} share the plugin name {doc.name!r}')
    return docs


class PluginProp(Prop):
    """A Prop whose enums live in the plugin namespace (except the global Protocol) and whose `external`
    objects (the spec's PluginOrdering / PluginPartial) are imported from Models."""

    external = False

    def __init__(self, *args, external=False, default=None):
        super().__init__(*args)
        self.external = external
        if default is not None:
            text = default if isinstance(default, str) else json.dumps(default, separators=(', ', ': '))
            if len(text) <= 80:
                note = f'Default: `{text}`.'.replace('*/', '* /')
                self.desc = (self.desc + ' ' if self.desc else '') + note

    def uses(self):
        if self.kind == 'fk':
            return {NS + '\\Models\\Shared\\ForeignKey'}
        if self.kind in ('enum', 'enumlist'):
            return {NS + '\\Enums\\Protocol'} if self.cls == 'Protocol' else set()
        if self.external:
            return {NS + '\\Models\\' + self.cls}
        return set()


class ConfigProp:
    """`config` on {Plugin}Input: the typed ConfigInput or an array sent as-is."""

    wire = 'config'
    php = 'config'
    kind = 'config'
    required = False
    encrypted = False
    write_only = False
    const = None
    external = False

    def __init__(self, cls, required):
        self.cls = cls
        self.desc = 'The plugin configuration; an array is sent as-is (explicit nulls, vault references).'
        if required:
            self.desc += ' ' + REQUIRED_NOTE

    def native(self, input_mode):
        return f'{self.cls}|array|null'

    def nullable(self, input_mode):
        return True

    def phpdoc(self, input_mode):
        return f'{self.cls}|array<string, mixed>|null'

    def writer(self, input_mode):
        return f'$this->config instanceof {self.cls} ? $this->config->toArray() : $this->config'

    def uses(self):
        return set()


def names_for(doc):
    """(owner, location) -> class name for every nested object and enum of the config, plus pointers."""
    nested, enums, pointers = {}, {}, {}
    taken = {doc.plugin + 'Input', doc.plugin + 'Config', doc.plugin + 'ConfigInput'}

    def name(location):
        bare = re.sub(r'\[\]|\{\}', '', location)
        chosen = NAMES.get((doc.slug, location), doc.plugin + ''.join(pascal(s) for s in bare.split('.')))
        if chosen in taken:
            raise SystemExit(f'{doc.rel}: derived class name {chosen} for {location} is taken; add a NAMES entry')
        taken.add(chosen)
        return chosen

    def walk(node, location, pointer):
        for wire, child in node.get('properties', {}).items():
            loc = f'{location}.{wire}' if location else wire
            ptr = f'{pointer}/properties/{escape(wire)}'
            kind = child.get('type') or ('object' if 'properties' in child else None)
            if 'enum' in child:
                enums[(doc.plugin, loc)] = name(loc)
                pointers[enums[(doc.plugin, loc)]] = (ptr, child)
            elif kind == 'object' and set(child.get('properties', {})) == {'id'}:
                continue
            elif kind == 'object' and 'properties' in child:
                nested[(doc.plugin, loc)] = name(loc)
                pointers[nested[(doc.plugin, loc)]] = (ptr, child)
                walk(child, loc, ptr)
            elif kind == 'object' and isinstance(child.get('additionalProperties'), dict) and 'properties' in child['additionalProperties']:
                key = (doc.plugin, loc + '{}')
                nested[key] = name(loc + '{}')
                pointers[nested[key]] = (ptr + '/additionalProperties', child['additionalProperties'])
                walk(child['additionalProperties'], loc, ptr + '/additionalProperties')
            elif kind == 'array' and isinstance(child.get('items'), dict):
                items = child['items']
                if 'enum' in items:
                    enums[(doc.plugin, loc + '[]')] = name(loc + '[]')
                    pointers[enums[(doc.plugin, loc + '[]')]] = (ptr + '/items', items)
                elif 'properties' in items:
                    nested[(doc.plugin, loc + '[]')] = name(loc + '[]')
                    pointers[nested[(doc.plugin, loc + '[]')]] = (ptr + '/items', items)
                    walk(items, loc, ptr + '/items')

    walk(doc.schema['properties']['config'], '', '#/properties/config')
    return nested, enums, pointers


def props_of(doc, node, location, nested, enums):
    required = set(node.get('required', []))
    props = [PluginProp(doc.plugin, wire, child, wire in required, nested, enums, location, default=child.get('default'))
             for wire, child in node['properties'].items()]
    return [p for p in props if p.required] + [p for p in props if not p.required]


def name_const(doc):
    return ["    /** The plugin's wire name (the doc's front-matter `url`). */",
            f'    public const string NAME = {php_string(doc.name)};', '']


def render_enum(doc, cls, pointer, node):
    values = node['enum']
    backing = 'int' if node.get('type') == 'integer' else 'string'
    if any(isinstance(v, int) for v in values) != (backing == 'int'):
        raise SystemExit(f'{doc.rel}: enum {pointer} mixes value types with type {node.get("type")!r}')
    cases = [case_name(v) for v in values]
    if len(set(cases)) != len(cases) or 'Class' in cases:
        raise SystemExit(f'{doc.rel}: enum {pointer} values {values} give clashing case names {cases}; extend case_name()')
    location = pointer.replace('#/properties/', '').replace('/properties/', '.').replace('/items', '[]')
    lines = ['<?php', '', 'declare(strict_types=1);', '', f'namespace {doc.namespace};', '',
             f'use {PLUGIN_SCHEMA};', '', '/**', f' * Values of `{location}` in the {doc.title.replace(" Configuration Reference", "")} doc.']
    desc = first_sentence(node.get('description', ''))
    if desc:
        lines += [' *', f' * {desc}']
    lines += [' */', doc.attribute(pointer)[1], f'enum {cls}: {backing}', '{']
    for case, value in zip(cases, values):
        lines.append(f'    case {case} = {value if backing == "int" else php_string(value)};')
    lines += ['}', '']
    return '\n'.join(lines)


def generate(doc):
    nested, enums, pointers = names_for(doc)
    config_node = doc.schema['properties']['config']
    root_required = set(doc.schema.get('required', []))
    plugin = doc.plugin
    label = doc.title.replace(' Plugin Configuration Reference', '')
    min_version = [f'Available from Kong Gateway {doc.min_version} (doc `min_version`).'] if doc.min_version else []
    partials = [f'Supports Partials: ' + '; '.join(
        f"`{p['name']}` for `{'`, `'.join(p['paths'])}`" for p in doc.schema.get('x-supported-partials', [])) + '.'] \
        if doc.schema.get('x-supported-partials') else []

    # Enums.
    for (owner, loc), cls in enums.items():
        pointer, node = pointers[cls]
        write(f'{doc.directory}/{cls}.php', render_enum(doc, cls, pointer, node))

    # Nested config objects (output DTOs, reused by the inputs).
    for (owner, loc), cls in nested.items():
        pointer, node = pointers[cls]
        location = re.sub(r'\[\]|\{\}', '', loc)
        props = props_of(doc, node, location, nested, enums)
        ndoc = [f'The `config.{loc}` object of the {label} plugin (doc `{doc.rel}`).']
        write(f'{doc.directory}/{cls}.php', render(cls, pointer, ndoc, props, False, sibling_ns=doc.namespace,
                                                    attribute=doc.attribute(pointer), encrypted_note=ENCRYPTED_NOTE))

    config_props = props_of(doc, config_node, '', nested, enums)

    # {Plugin}Config (output).
    cfg = plugin + 'Config'
    cdoc = [f'Typed `config` of a `{doc.name}` plugin ({label}; doc `{doc.rel}`).', '',
            f'Read it from a returned Plugin with `{cfg}::fromPlugin($plugin)` or `PluginRegistry::config($plugin)`.']
    tail = ['', '    /**', f'     * Reads the typed configuration of a `{doc.name}` Plugin returned by the Admin API.', '     *',
            f'     * @throws InvalidArgumentException     when $plugin is not a `{doc.name}` plugin',
            '     * @throws UnexpectedResponseException when its `config` does not match the plugin doc', '     */',
            '    #[Override]', '    public static function fromPlugin(Plugin $plugin): static', '    {',
            '        if ($plugin->name !== self::NAME) {',
            "            throw new InvalidArgumentException(sprintf('Expected a \"%s\" plugin, got \"%s\".', self::NAME, $plugin->name));",
            '        }', '', "        return self::fromArray(Data::asMap($plugin->config ?? [], 'config'));", '    }']
    write(f'{doc.directory}/{cfg}.php', render(
        cfg, '#/properties/config', cdoc, config_props, False, sibling_ns=doc.namespace, contract='PluginConfig',
        extra_uses={NS + '\\Plugins\\PluginConfig', NS + '\\Models\\Plugin', NS + '\\Exceptions\\InvalidArgumentException',
                    NS + '\\Exceptions\\UnexpectedResponseException'},
        attribute=doc.attribute('#/properties/config'), encrypted_note=ENCRYPTED_NOTE, head=name_const(doc), tail=tail))

    # {Plugin}ConfigInput.
    cin = plugin + 'ConfigInput'
    idoc = [f'Typed `config` request body of a `{doc.name}` plugin ({label}; doc `{doc.rel}`).', '',
            'Every field is optional because PATCH reuses it; null fields are not sent. To send an explicit null, or a',
            'vault reference to a non-string field, pass `config` as an array instead.']
    write(f'{doc.directory}/{cin}.php', render(
        cin, '#/properties/config', idoc, config_props, True, sibling_ns=doc.namespace,
        attribute=doc.attribute('#/properties/config'), encrypted_note=ENCRYPTED_NOTE, head=name_const(doc),
        required_note=REQUIRED_NOTE))

    # {Plugin}Input: config + the doc's scope fields + the spec's generic Plugin fields.
    absent = set(ROOT_FIELDS) - set(doc.schema['properties'])
    generic = []
    for wire, node in SPEC_PLUGIN['properties'].items():
        if wire in ('name', 'config') or wire in absent:
            continue
        generic.append(PluginProp('Plugin', wire, node, False, SPEC_NESTED, SPEC_ENUMS, '',
                                  external=wire in ('ordering', 'partials')))
    scopes = [f for f in ('consumer', 'consumer_group', 'route', 'service') if f in doc.schema['properties']]
    pdoc = [f'Request body for a `{doc.name}` plugin ({label}; doc `{doc.rel}`).', '',
            first_sentence(doc.description, 400), '',
            'Use it with `create()`, `update()` or `upsert()` of `plugins()` or of the plugin resources nested under',
            'Services, Routes, Consumers and Consumer Groups. `name` is sent automatically; null fields are not sent.',
            'Scopes the doc allows: ' + (', '.join(f'`{s}`' for s in scopes) if scopes else 'none (global only)') + '.']
    pdoc += partials + min_version
    write(f'{doc.directory}/{plugin}Input.php', render(
        plugin + 'Input', '#', pdoc, [ConfigProp(cin, 'config' in root_required)] + generic, True,
        sibling_ns=doc.namespace, contract='TypedPluginInput', extra_uses={NS + '\\Plugins\\TypedPluginInput'},
        attribute=doc.attribute('#'), head=name_const(doc), array_head=["            'name' => self::NAME,"]))

    # Fixture: every config property.
    data = fixture_object(config_node, '')
    write(f'tests/Fixtures/Plugins/{doc.category}/{doc.slug}.json', json.dumps(data, indent=2, sort_keys=True) + '\n')


def registry(docs):
    ordered = sorted(docs, key=lambda d: d.name)
    uses = sorted({f'{d.namespace}\\{d.plugin}Config' for d in docs} |
                  {NS + '\\Models\\Plugin', NS + '\\Exceptions\\UnexpectedResponseException'})
    lines = ['<?php', '', 'declare(strict_types=1);', '', f'namespace {NS}\\Plugins;', '']
    lines += [f'use {u};' for u in uses] + ['']
    lines += ['/**', ' * The plugins with a doc in `resources/kong-admin-api/plugins/` and typed classes in this package',
              ' * (generated by `tools/generator/plugins.py`; blocked docs are listed in `docs/plugin-notes.md`).', ' */',
              'final readonly class PluginRegistry', '{', '    /** @codeCoverageIgnore Static holder; never instantiated. */',
              '    private function __construct()', '    {', '    }', '', '    /**',
              '     * Wire name => typed config class, for every documented plugin.', '     *',
              '     * @return array<string, class-string<PluginConfig>>', '     */', '    public static function configs(): array', '    {',
              '        return [']
    lines += [f'            {d.plugin}Config::NAME => {d.plugin}Config::class,' for d in ordered]
    lines += ['        ];', '    }', '', '    /**', '     * Whether $name is a documented plugin with typed classes.', '     */',
              '    public static function has(string $name): bool', '    {', '        return isset(self::configs()[$name]);', '    }', '',
              '    /**', '     * The typed configuration of $plugin, or null when the plugin has no doc in this package.', '     *',
              '     * @throws UnexpectedResponseException when its `config` does not match the plugin doc', '     */',
              '    public static function config(Plugin $plugin): ?PluginConfig', '    {',
              '        $class = self::configs()[$plugin->name] ?? null;', '',
              '        return $class === null ? null : $class::fromPlugin($plugin);', '    }', '}', '']
    write('src/Plugins/PluginRegistry.php', '\n'.join(lines))


def readme_table(docs):
    rows = ['| Category | Plugin (`name`) | Namespace `Aybarsm\\Kong\\AdminApi\\Plugins\\…` |', '|---|---|---|']
    for d in sorted(docs, key=lambda d: (d.category, d.slug)):
        rows.append(f'| {d.category} | {d.title.replace(" Plugin Configuration Reference", "")} (`{d.name}`) | '
                    f'`{d.category}\\{d.plugin}\\{d.plugin}Input`, `…Config`, `…ConfigInput` |')
    text = README.read_text()
    block = '<!-- plugins:start -->\n' + '\n'.join(rows) + '\n<!-- plugins:end -->'
    updated, count = re.subn(r'<!-- plugins:start -->.*?<!-- plugins:end -->', lambda _: block, text, flags=re.S)
    if count != 1:
        raise SystemExit(f'{README}: plugins:start/plugins:end markers missing')
    if updated != text:
        README.write_text(updated)
        print('README.md')


def main():
    docs = load_docs()
    for doc in docs:
        generate(doc)
    registry(docs)
    finalize()
    readme_table(docs)


if __name__ == '__main__':
    main()
