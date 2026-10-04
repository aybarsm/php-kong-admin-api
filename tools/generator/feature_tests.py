"""Generates one Pest feature test per CRUD resource config (see resources.py)."""
import re
from models import NS, schema_node, write
from resources import has_twin


def snake(name):
    return re.sub(r'(?<=[a-z0-9])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][a-z])', '_', name).lower()


def lcfirst(s):
    return s[0].lower() + s[1:]


def php_str(s):
    return "'" + s.replace('\\', '\\\\').replace("'", "\\'") + "'"


def feature_test(cfg, accessor_expr, parent_path='', parent_cls=None, covers_extra=()):
    cls, model = cfg['cls'], cfg['model']
    nested = cfg['nested']
    seg = cfg['segments']
    base = parent_path + '/' + seg
    item_arg = cfg['item_arg']
    schema = cfg['body_schema']['PATCH']
    node = schema_node(schema)
    key = (node.get('required') or sorted(node['properties']))[0]
    body = f"[{php_str(key)} => 'value']"
    fx = snake(model)
    get_model = cfg.get('get_returns', model)
    get_fx = snake(get_model)
    both = has_twin('GET', cfg['collection'])
    ns_res = NS + ('\\Resources\\Nested\\' if nested else '\\Resources\\')
    fn = lcfirst(cls) + 'Under' + ('Test' if not nested else 'NestedTest')
    covers = [cls] + list(covers_extra)
    uses = sorted({ns_res + cls, NS + '\\Models\\' + model, NS + '\\Models\\' + get_model, NS + '\\Models\\' + model + 'Input',
                   NS + '\\Exceptions\\NotFoundException', NS + '\\Exceptions\\InvalidArgumentException',
                   NS + '\\Pagination\\ListOptions', NS + '\\Pagination\\TagFilter',
                   NS + '\\Tests\\Support\\Fixture', NS + '\\Tests\\Support\\MockKong', NS + '\\KongClient'} | set(cfg.get('test_uses', [])))
    L = ['<?php', '', 'declare(strict_types=1);', '']
    L += [f'use {u};' for u in uses] + ['']
    L += [f"covers({', '.join(c + '::class' for c in covers)});", '']
    L += [f'function {fn}(KongClient $client): {cls}', '{', f'    return {accessor_expr};', '}', '']
    L += [f"it('sends every operation to the spec path', function (string $operation, string $method, string $path, ?string $body): void {{",
          '    $kong = MockKong::queue(match ($operation) {',
          f"        'list' => MockKong::json(200, ['data' => [Fixture::get('{fx}')]]),",
          "        'delete' => MockKong::raw(204),",
          *([f"        'get' => MockKong::json(200, Fixture::get('{get_fx}')),"] if get_fx != fx else []),
          f"        default => MockKong::json(200, Fixture::get('{fx}')),",
          '    });',
          f'    $resource = {fn}($kong->client);', '',
          '    match ($operation) {',
          "        'list' => $resource->list(),",
          "        'get' => $resource->get('item 1'),",
          f"        'create' => $resource->create({body}),",
          f"        'update' => $resource->update('item 1', {body}),",
          f"        'upsert' => $resource->upsert('item 1', new {model}Input()),",
          "        'delete' => $resource->delete('item 1'),",
          "        default => throw new LogicException('Unknown operation ' . $operation),",
          '    };', '',
          '    expect($kong->lastRequest()->getMethod())->toBe($method)',
          '        ->and($kong->lastRequest()->getUri()->getPath())->toBe($path)',
          '        ->and($kong->lastRequest()->getBody()->__toString())->toBe($body ?? \'\');',
          '})->with([',
          f"    'list' => ['list', 'GET', '{base}', null],",
          f"    'get' => ['get', 'GET', '{base}/item%201', null],",
          f"    'create' => ['create', 'POST', '{base}', '{{\"{key}\":\"value\"}}'],",
          f"    'update' => ['update', 'PATCH', '{base}/item%201', '{{\"{key}\":\"value\"}}'],",
          f"    'upsert' => ['upsert', 'PUT', '{base}/item%201', '{{}}'],",
          f"    'delete' => ['delete', 'DELETE', '{base}/item%201', null],",
          ']);', '']
    L += ["it('lists one page with options and walks every page', function (): void {",
          '    $kong = MockKong::queue(',
          f"        MockKong::json(200, ['data' => [Fixture::get('{fx}')], 'offset' => 'p1']),",
          f"        MockKong::json(200, ['data' => [Fixture::get('{fx}')], 'offset' => 'p2']),",
          f"        MockKong::json(200, ['data' => [Fixture::get('{fx}')]]),",
          '    );',
          f'    $resource = {fn}($kong->client);', '',
          "    $page = $resource->list(new ListOptions(size: 1, tags: TagFilter::allOf('a', 'b')));",
          "    $rest = iterator_to_array($resource->all(new ListOptions(size: 1, offset: 'p1')));", '',
          f"    expect($page->data)->toEqual([{model}::fromArray(Fixture::get('{fx}'))])",
          "        ->and($page->offset)->toBe('p1')",
          "        ->and($kong->queryAt(0))->toBe(['size' => '1', 'tags' => 'a,b'])",
          "        ->and($rest)->toHaveCount(2)",
          "        ->and($kong->queryAt(1))->toBe(['size' => '1', 'offset' => 'p1'])",
          "        ->and($kong->queryAt(2))->toBe(['size' => '1', 'offset' => 'p2'])",
          f"        ->and($kong->requestAt(2)->getUri()->getPath())->toBe('{base}');",
          '});', '']
    L += [f"it('maps the response to {get_model}', function (): void {{",
          f"    $kong = MockKong::queue(MockKong::json(200, Fixture::get('{get_fx}')));", '',
          f"    expect({fn}($kong->client)->get('x'))->toEqual({get_model}::fromArray(Fixture::get('{get_fx}')));",
          '});', '']
    if both:
        L += ["it('prefixes the workspace', function (): void {",
              f"    $kong = MockKong::queue(MockKong::json(200, Fixture::get('{get_fx}')));", '',
              f"    {fn}($kong->client->inWorkspace('team-a'))->get('x');", '',
              f"    expect($kong->lastRequest()->getUri()->getPath())->toBe('/team-a{base}/x');",
              '});', '']
    else:
        L += ["it('never prefixes a workspace on these global-only paths', function (): void {",
              f"    $kong = MockKong::queue(MockKong::json(200, Fixture::get('{get_fx}')));", '',
              f"    {fn}($kong->client->inWorkspace('team-a'))->get('x');", '',
              f"    expect($kong->lastRequest()->getUri()->getPath())->toBe('{base}/x');",
              '});', '']
    L += ["it('rejects an empty ID before sending anything', function (): void {",
          '    $kong = MockKong::queue();', '',
          f"    expect(fn (): {get_model} => {fn}($kong->client)->get(''))->toThrow(InvalidArgumentException::class)",
          '        ->and($kong->requestCount())->toBe(0);',
          '});', '',
          "it('maps 404 to NotFoundException', function (): void {",
          '    $kong = MockKong::queue(MockKong::raw(404));', '',
          f"    expect(fn (): {get_model} => {fn}($kong->client)->get('missing'))",
          f"        ->toThrow(NotFoundException::class, 'GET {base}/missing failed with HTTP 404.');",
          '});', '']
    for extra in cfg.get('test_extra', []):
        L += extra.replace('{fn}', fn).split('\n') + ['']
    L = L[:-1] + ['']
    path = 'tests/Feature/Resources/' + ('Nested/' if nested else '') + cls + 'Test.php'
    write(path, '\n'.join(L))
    return path
