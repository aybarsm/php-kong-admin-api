"""Spec-driven DTO generator for aybarsm/kong-admin-api (see tools/generator/README.md).

Emits output DTOs (Models\\X), input DTOs (Models\\XInput) and nested DTOs in the exact style of the
approved reference classes (Service / ServiceInput). Every type decision is taken from the spec schema;
anything the spec does not determine (class names for inline objects, enum class per location) comes
from the explicit tables passed in by the caller.
"""
import json
import re
import subprocess
from pathlib import Path

ROOT = str(Path(__file__).resolve().parents[2])
NS = 'Aybarsm\\Kong\\AdminApi'


def _spec_file():
    """The canonical spec named by KongSpec::SPEC_FILE; stops with the exact expected path when missing."""
    source = Path(ROOT, 'src', 'KongSpec.php').read_text()
    match = re.search(r"SPEC_FILE = '([^']+)'", source)
    if match is None:
        raise SystemExit('KongSpec::SPEC_FILE not found in src/KongSpec.php')
    path = Path(ROOT, 'resources', 'kong-admin-api', match.group(1))
    if not path.is_file() or path.stat().st_size == 0:
        raise SystemExit(f'Missing Kong spec: {path}')
    return path


SPEC_PATH = _spec_file()
SPEC = json.loads(SPEC_PATH.read_text())
WRITTEN = []

# Component schema name -> Models class, for properties that `$ref` another component schema.
# Phases register the components they reference (e.g. {'Consumer': 'Consumer'}).
REFS = {}


def ref_class(ref):
    name = ref.rsplit('/', 1)[-1]
    if name not in REFS:
        raise KeyError(f'REFS has no class for component schema {name!r}')
    return REFS[name]


def camel(name):
    """snake_case (and the spec's dotted keys such as `config.limit`) to camelCase."""
    parts = re.split(r'[_.]', name)
    return parts[0] + ''.join(p[:1].upper() + p[1:] for p in parts[1:])


def pointer_node(pointer):
    node = SPEC
    for seg in pointer[2:].split('/'):
        seg = seg.replace('~1', '/').replace('~0', '~')
        node = node[int(seg)] if isinstance(node, list) else node[seg]
    if node.get('type') == 'array' and 'items' in node:
        node = node['items']
    return node


def schema_node(name):
    return pointer_node(name) if name.startswith('#/') else SPEC['components']['schemas'][name]


def first_sentence(text, limit=110):
    if not text:
        return ''
    text = ' '.join(text.split())
    m = re.match(r'(.+?\.)(\s|$)', text)
    s = m.group(1) if m else text
    if len(s) > limit:
        s = s[: limit - 1].rstrip() + '…'
    return s.replace('*/', '* /')


class Prop:
    """One schema property resolved to PHP types and code fragments."""

    def __init__(self, cls, wire, node, required, nested_names, enum_names, location):
        self.wire = wire
        self.php = camel(wire)
        self.node = node
        self.required = required
        self.encrypted = bool(node.get('x-encrypted'))
        self.write_only = bool(node.get('writeOnly'))
        self.desc = first_sentence(node.get('description', ''))
        loc = location + '.' + wire if location else wire
        self.loc = loc
        t = node.get('type')
        self.kind = None
        if '$ref' in node:
            self.kind = 'obj'
            self.cls = ref_class(node['$ref'])
        elif t == 'array' and '$ref' in node.get('items', {}):
            self.kind = 'objlist'
            self.cls = ref_class(node['items']['$ref'])
        elif node.get('x-foreign'):
            self.kind = 'fk'
            self.cls = 'ForeignKey'
        elif 'enum' in node:
            self.kind = 'enum'
            self.cls = enum_names[(cls, loc)]
            self.enum_int = t == 'integer'
        elif t == 'array':
            items = node.get('items', {})
            it = items.get('type')
            if 'enum' in items:
                self.kind = 'enumlist'
                self.cls = enum_names[(cls, loc + '[]')]
            elif it == 'string':
                self.kind = 'stringlist'
            elif it == 'integer':
                self.kind = 'intlist'
            elif it == 'object' and 'properties' in items:
                self.kind = 'objlist'
                self.cls = nested_names[(cls, loc + '[]')]
            else:
                raise ValueError(f'unsupported array {cls}.{loc}: {items}')
        elif t == 'object':
            ap = node.get('additionalProperties')
            if 'properties' in node:
                self.kind = 'obj'
                self.cls = nested_names[(cls, loc)]
            elif isinstance(ap, dict) and ap.get('type') == 'array' and ap.get('items', {}).get('type') == 'string':
                self.kind = 'stringlistmap'
            elif isinstance(ap, dict) and ap.get('type') == 'string':
                self.kind = 'stringmap'
            else:
                self.kind = 'freeform'
        elif t == 'string':
            self.kind = 'string'
        elif t == 'integer':
            self.kind = 'int'
        elif t == 'number':
            self.kind = 'float'
        elif t == 'boolean':
            self.kind = 'bool'
        else:
            raise ValueError(f'unsupported type {cls}.{loc}: {node}')

    # ---- types -------------------------------------------------------------------------------
    def native(self, input_mode):
        base = {
            'fk': 'ForeignKey', 'enum': self.__dict__.get('cls'), 'obj': self.__dict__.get('cls'),
            'string': 'string', 'int': 'int', 'float': 'float', 'bool': 'bool',
        }.get(self.kind, 'array')
        if input_mode and self.kind == 'fk':
            return 'ForeignKey|string|null'
        if self.nullable(input_mode):
            return '?' + base
        return base

    def nullable(self, input_mode):
        return input_mode or not self.required

    def phpdoc(self, input_mode):
        k = self.kind
        null = '|null' if self.nullable(input_mode) else ''
        if k == 'stringlist':
            return 'list<string>' + null
        if k == 'intlist':
            return 'list<int>' + null
        if k == 'enumlist':
            return f'list<{self.cls}>' + null
        if k == 'objlist':
            return f'list<{self.cls}>' + null
        if k == 'freeform':
            return 'array<array-key, mixed>' + null
        if k == 'stringlistmap':
            return 'array<array-key, list<string>>' + null
        if k == 'stringmap':
            return 'array<array-key, string>' + null
        if k == 'fk' and input_mode:
            return 'ForeignKey|string|null'
        return None

    # ---- code --------------------------------------------------------------------------------
    def reader(self):
        """Return (pre_statement or None, expression) reading $data[wire]."""
        w, k, req = self.wire, self.kind, self.required
        v = '$' + self.php
        if k in ('fk', 'obj'):
            cls = self.cls
            if req:
                return None, f"{cls}::fromArray(Data::map($data, '{w}'))"
            return f"{v} = Data::mapOrNull($data, '{w}');", f"{v} === null ? null : {cls}::fromArray({v})"
        if k == 'objlist':
            return f"{v} = Data::listOfMapsOrNull($data, '{w}');", f"{v} === null ? null : array_map({self.cls}::fromArray(...), {v})"
        simple = {
            'string': 'string' if req else 'stringOrNull',
            'int': 'int' if req else 'intOrNull',
            'bool': 'bool' if req else 'boolOrNull',
            'float': 'floatOrNull',
            'stringlist': 'stringListOrNull',
            'intlist': 'intListOrNull',
            'freeform': 'freeFormOrNull',
            'stringlistmap': 'stringListMapOrNull',
            'stringmap': 'stringMapOrNull',
        }
        if k == 'float' and req:
            raise ValueError('required float unsupported')
        if k == 'enum':
            if req:
                raise ValueError('required enum unsupported')
            return None, f"Data::enumOrNull($data, '{w}', {self.cls}::class)"
        if k == 'enumlist':
            return None, f"Data::enumListOrNull($data, '{w}', {self.cls}::class)"
        if k in simple and (req is False or k in ('string', 'int', 'bool')):
            return None, f"Data::{simple[k]}($data, '{w}')"
        raise ValueError(f'no reader for {self.loc} {k} req={req}')

    def writer(self, input_mode):
        p, k = '$this->' + self.php, self.kind
        nullsafe = '?->' if self.nullable(input_mode) else '->'
        if k == 'fk' and input_mode:
            return f'{p} === null ? null : ForeignKey::of({p})->toArray()'
        if k in ('fk', 'obj'):
            return f'{p}{nullsafe}toArray()'
        if k == 'enum':
            return f'{p}{nullsafe}value'
        if k == 'enumlist':
            return f'Data::enumValues({p})'
        if k == 'objlist':
            return f'Data::toArrays({p})'
        return p

    def uses(self):
        if self.kind == 'fk':
            return {NS + '\\Models\\Shared\\ForeignKey'}
        if self.kind in ('enum', 'enumlist'):
            return {NS + '\\Enums\\' + self.cls}
        return set()


def build_props(cls, schema_name, nested_names, enum_names, location):
    node = schema_node(schema_name)
    required = set(node.get('required', []))
    props = [Prop(cls, w, n, w in required, nested_names, enum_names, location) for w, n in node['properties'].items()]
    # required first, then spec (alphabetical) order
    return [p for p in props if p.required] + [p for p in props if not p.required]


def render(cls, schema_name, doc, props, input_mode, extra_uses=(), sibling_ns=None):
    uses = {NS + '\\Attributes\\Schema', NS + '\\Internal\\Data', 'Override'}
    uses.add(NS + ('\\Contracts\\Input' if input_mode else '\\Contracts\\Model'))
    for p in props:
        uses |= p.uses()
        if p.kind in ('obj', 'objlist') and sibling_ns:
            uses.add(sibling_ns + '\\' + p.cls)
    if any(p.encrypted for p in props) and input_mode:
        uses.add('SensitiveParameter')
    uses |= set(extra_uses)
    if input_mode:
        props = [p for p in props]  # inputs carry writeOnly fields too
    else:
        props = [p for p in props if not p.write_only]

    ns = NS + '\\Models' if sibling_ns is None else sibling_ns
    uses = sorted(u for u in uses if u.rsplit('\\', 1)[0] != ns or u == 'Override')

    lines = ['<?php', '', 'declare(strict_types=1);', '', f'namespace {ns};', '']
    lines += [f'use {u};' for u in uses] + ['']
    lines += ['/**'] + [(' * ' + l).rstrip() for l in doc] + [' */']
    attr_name = schema_name.replace("'", "\\'")
    lines += [f"#[Schema('{attr_name}')]"]
    contract = 'Input' if input_mode else 'Model'
    lines += [f'final readonly class {cls} implements {contract}', '{']

    enc = [p.php for p in props if p.encrypted]
    if enc:
        lines += ['    /** Properties the spec marks `x-encrypted`; redacted in __debugInfo(). */',
                  '    private const array ENCRYPTED = [' + ', '.join(f"'{e}'" for e in enc) + '];', '']

    # constructor docblock
    doc_params = []
    width = max(len(p.phpdoc(input_mode) or p.native(input_mode).lstrip('?') + ('|null' if p.nullable(input_mode) else '')) for p in props)
    namew = max(len(p.php) for p in props) + 1
    for p in props:
        t = p.phpdoc(input_mode) or (p.native(input_mode).lstrip('?') + ('|null' if p.nullable(input_mode) else ''))
        d = p.desc
        if input_mode and p.required:
            d = (d + ' ' if d else '') + 'Required by the spec on create.'
        doc_params.append(f'     * @param {t.ljust(width)} ${p.php.ljust(namew - 1)} {d}'.rstrip())
    lines += ['    /**'] + doc_params + ['     */', '    public function __construct(']
    for p in props:
        default = ' = null' if p.nullable(input_mode) else ''
        if input_mode and p.encrypted:
            lines.append('        #[SensitiveParameter]')
        lines.append(f'        public {p.native(input_mode)} ${p.php}{default},')
    lines += ['    ) {', '    }', '']

    if not input_mode:
        pre, args = [], []
        for p in props:
            st, expr = p.reader()
            if st:
                pre.append('        ' + st)
            args.append(f'            {p.php}: {expr},')
        lines += ['    /**', '     * @param array<string, mixed> $data', '     */', '    #[Override]',
                  '    public static function fromArray(array $data): static', '    {']
        lines += pre + ([''] if pre else [])
        lines += ['        return new self('] + args + ['        );', '    }', '']

    lines += ['    /**', '     * @return array<string, mixed>', '     */', '    #[Override]',
              '    public function toArray(): array', '    {', '        return Data::withoutNulls([']
    for p in props:
        lines.append(f"            '{p.wire}' => {p.writer(input_mode)},")
    lines += ['        ]);', '    }']

    if enc:
        lines += ['', '    /**', '     * Redacts `x-encrypted` values.', '     *', '     * @return array<string, mixed>', '     */',
                  '    public function __debugInfo(): array', '    {', '        $values = get_object_vars($this);',
                  '        foreach (self::ENCRYPTED as $property) {', '            if ($values[$property] !== null) {',
                  "                $values[$property] = '***';", '            }', '        }', '', '        return $values;', '    }']
    lines += ['}', '']
    return '\n'.join(lines)


def write(path, content):
    """Writes a repo-relative file and records it for finalize()."""
    target = Path(ROOT, path)
    target.parent.mkdir(parents=True, exist_ok=True)
    target.write_text(content)
    if path not in WRITTEN:
        WRITTEN.append(path)


def finalize():
    """Formats every written PHP file with the project's php-cs-fixer config and lists them."""
    php = [p for p in WRITTEN if p.endswith('.php')]
    if php:
        subprocess.run([str(Path(ROOT, 'vendor', 'bin', 'php-cs-fixer')), 'fix', '--quiet', '--config',
                        str(Path(ROOT, '.php-cs-fixer.dist.php')), '--path-mode=intersection', *php],
                       cwd=ROOT, check=True)
    for p in WRITTEN:
        print(p)


def generate_entity(cls, schema, doc, nested_names, enum_names, input_doc=None, with_input=True, input_schema=None,
                    with_output=True):
    """Writes Models/{cls}.php, Models/{cls}Input.php and every nested DTO the schema needs.

    `with_output=False` writes only the input (for inline request bodies that have no response twin).
    """
    written = []
    props = build_props(cls, schema, nested_names, enum_names, '')
    if with_output:
        write(f'src/Models/{cls}.php', render(cls, schema, doc, props, False))
        written.append(f'src/Models/{cls}.php')
    if with_input:
        idoc = input_doc or [
            f'Request body for creating, updating or upserting {doc[0].split(" as returned")[0][0].lower() + doc[0].split(" as returned")[0][1:]}',
            f'(spec schema `{schema}`).', '',
            'Every field is optional because PATCH reuses the schema; null fields are not sent.',
            'To send an explicit null, pass an array instead.',
        ]
        write(f'src/Models/{cls}Input.php', render(cls + 'Input', input_schema or schema, idoc, props, True))
        written.append(f'src/Models/{cls}Input.php')
    # nested objects reachable from this schema's properties (inline objects named in nested_names)
    def walk(owner_props, pointer):
        for p in owner_props:
            key = (cls, p.loc + '[]') if p.kind == 'objlist' else (cls, p.loc)
            if p.kind not in ('obj', 'objlist') or key not in nested_names:
                continue
            ncls = nested_names[key]
            npointer = pointer + '/properties/' + p.wire
            nprops = build_props(cls, npointer, nested_names, enum_names, p.loc)
            ndoc = [f'The `{key[1]}` object of {cls} (spec `{schema}.{key[1]}`).']
            write(f'src/Models/{ncls}.php', render(ncls, npointer, ndoc, nprops, False))
            written.append(f'src/Models/{ncls}.php')
            walk(nprops, npointer)

    walk(props, '#/components/schemas/' + schema if not schema.startswith('#/') else schema)
    return written
