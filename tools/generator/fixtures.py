"""Builds spec-shaped JSON fixtures with every property present (writeOnly excluded)."""
import json
import uuid
from models import schema_node, write

NAMESPACE = uuid.UUID('6f1c1a52-6f86-4f53-9a0c-3d8f2a7f1b11')


def uid(seed):
    return str(uuid.uuid5(NAMESPACE, seed))


def value(node, path):
    if node.get('x-foreign'):
        return {'id': uid(path)}
    if 'enum' in node:
        return node['enum'][0]
    t = node.get('type')
    ex = node.get('example')
    if t == 'array':
        items = node.get('items', {})
        if 'enum' in items:
            return [items['enum'][0]]
        return [value(items, path + '[]')]
    if t == 'object':
        if 'properties' in node:
            return obj(node, path)
        ap = node.get('additionalProperties')
        if isinstance(ap, dict) and ap.get('type') == 'array':
            return {'x-header': ['value-1', 'value-2']}
        if isinstance(ap, dict) and ap.get('type') == 'string':
            return {'key': 'value'}
        return {'option': 'value', 'enabled': True, 'nested': {'limit': 5}}
    if t == 'string':
        if isinstance(ex, str):
            return ex
        if path.endswith('.id') or path == 'id':
            return uid(path)
        return path.split('.')[-1].replace('[]', '') + '-value'
    if t == 'integer':
        if isinstance(ex, int):
            return ex
        if 'created_at' in path or 'updated_at' in path:
            return 1706598432
        return max(node.get('minimum', 1), 1) if node.get('minimum', 1) <= 3 else node['minimum']
    if t == 'number':
        if 'created_at' in path or 'updated_at' in path:
            return 1706598432.5
        return 1.5
    if t == 'boolean':
        return True
    raise ValueError(path)


def obj(node, path):
    out = {}
    for name, child in node['properties'].items():
        if child.get('writeOnly'):
            continue
        out[name] = value(child, f'{path}.{name}' if path else name)
    return out


def fixture(name, schema):
    data = obj(schema_node(schema), '')
    write(f'tests/Fixtures/{name}.json', json.dumps(data, indent=2, sort_keys=True) + '\n')
    return name
