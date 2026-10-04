"""Generates CRUD(+PUT) resource classes in the style of the approved Services/Routes resources."""
from models import NS, SPEC, write


def spec_op(method, path):
    op = SPEC['paths'][path][method.lower()]
    return op['operationId']


def has_twin(method, path):
    twin = SPEC['paths'].get('/{workspace}' + path, {})
    return method.lower() in twin


def resource(cfg):
    """cfg keys:
    cls, nested (bool), doc (list), segments (list[str] child segments), collection (spec path), item (spec path),
    item_arg, item_arg_doc, entity (singular noun for docs), model (class), map (callable expr or None),
    returns (php return type), input (union type string), input_imports (list), methods (list),
    list_query (list of (php_arg, wire, php_type, doc)), body_schema (dict method->schema name)
    """
    cls = cfg['cls']
    ns = NS + ('\\Resources\\Nested' if cfg['nested'] else '\\Resources')
    model = cfg['model']
    ret = cfg.get('returns', model)
    mapper = cfg.get('map', f'{model}::fromArray(...)')
    mapone = cfg.get('mapone', f'{model}::fromArray')
    coll, item = cfg['collection'], cfg['item']
    seg_const = cfg['segments']
    methods = cfg.get('methods', ['list', 'all', 'get', 'create', 'update', 'upsert', 'delete'])
    entity = cfg['entity']
    plural = cfg.get('plural', entity + 's')
    item_arg = cfg['item_arg']
    inp = cfg['input']
    lq = cfg.get('list_query', [])

    uses = {NS + '\\Attributes\\Operation', NS + '\\Enums\\OperationScope', NS + '\\Exceptions\\KongApiException',
            NS + '\\Internal\\Transport'}
    uses |= set(cfg.get('imports', []))
    if cfg['nested']:
        uses.add(NS + '\\Resources\\AbstractResource')
    if any(m in methods for m in ('get', 'update', 'upsert', 'delete')):
        uses.add(NS + '\\Exceptions\\InvalidArgumentException')
    if 'list' in methods:
        uses |= {NS + '\\Pagination\\ListOptions', NS + '\\Pagination\\Page'}
    if 'all' in methods:
        uses.add('Generator')
    for extra in cfg.get('extra_uses', []):
        uses.add(extra)
    uses = sorted(uses)

    def scope(method, path):
        if path.startswith('/{workspace}/'):
            return 'OperationScope::WorkspaceOnly'
        return 'OperationScope::Both' if has_twin(method, path) else 'OperationScope::GlobalOnly'

    if cfg.get('segment_list'):
        segs = cfg['segment_list']
    else:
        segs = ['self::SEGMENT']

    lines = ['<?php', '', 'declare(strict_types=1);', '', f'namespace {ns};', '']
    lines += [f'use {u};' for u in uses] + ['']
    lines += ['/**'] + [(' * ' + l).rstrip() for l in cfg['doc']] + [' */']
    lines += [f'final readonly class {cls} extends AbstractResource', '{']
    if not cfg.get('segment_list'):
        lines += [f"    private const string SEGMENT = '{seg_const}';", '']

    def path_expr(sc, with_item):
        parts = segs + ([f'${item_arg}'] if with_item else [])
        return f'$this->path({sc}, ' + ', '.join(parts) + ')'

    lq_sig = ''.join(f', ?{t} ${a} = null' for a, w, t, d in lq)
    lq_query = ''
    if lq:
        lq_query = ', [' + ', '.join(f"'{w}' => ${a}" for a, w, t, d in lq) + ']'
    lq_doc = [f'     * @param {t}|null ${a} {d}' for a, w, t, d in lq]
    bodies = cfg.get('body_schema', {})

    def opattr(method, path):
        return f"    #[Operation(Transport::METHOD_{method}, '{path}', '{spec_op(method, path)}', {scope(method, path)})]"

    out = []
    if 'list' in methods:
        sc = scope('GET', coll)
        out += ['    /**', f'     * List one page of {plural} (operationId `{spec_op("GET", coll)}`).', '     *']
        if lq_doc:
            out += lq_doc + ['     *']
        out += [f'     * @return Page<{ret}>', '     *', '     * @throws KongApiException', '     */', opattr('GET', coll),
                f'    public function list(?ListOptions $options = null{lq_sig}): Page', '    {',
                f'        return $this->page({path_expr(sc, False)}, {mapper}, $options{lq_query});', '    }', '']
    if 'all' in methods:
        sc = scope('GET', coll)
        out += ['    /**', f'     * Lazily iterate every {entity} across all pages (operationId `{spec_op("GET", coll)}`).', '     *']
        if lq_doc:
            out += lq_doc + ['     *']
        out += [f'     * @return Generator<int, {ret}>', '     *', '     * @throws KongApiException', '     */', opattr('GET', coll),
                f'    public function all(?ListOptions $options = null{lq_sig}): Generator', '    {',
                f'        return $this->walk({path_expr(sc, False)}, {mapper}, $options{lq_query});', '    }', '']
    item_throw = f'     * @throws InvalidArgumentException when ${item_arg} is empty'
    if 'get' in methods:
        sc = scope('GET', item)
        get_ret = cfg.get('get_returns', ret)
        get_map = cfg.get('get_mapone', mapone)
        gq = cfg.get('get_query', [])
        gq_sig = ''.join(f', ?{t} ${a} = null' for a, w, t, d in gq)
        gq_doc = [f'     * @param {t}|null ${a} {d}' for a, w, t, d in gq]
        gq_arg = ['            [' + ', '.join(f"'{w}' => ${a}" for a, w, t, d in gq) + '],'] if gq else []
        get_doc = cfg.get('get_doc', [])
        out += ['    /**', f'     * Get {cfg["article"]} {entity} by {cfg["item_arg_doc"]} (operationId `{spec_op("GET", item)}`).', '     *']
        if get_doc:
            out += [('     * ' + l).rstrip() for l in get_doc] + ['     *']
        if gq_doc:
            out += gq_doc + ['     *']
        out += ['     * @throws KongApiException', item_throw, '     */', opattr('GET', item),
                f'    public function get(string ${item_arg}{gq_sig}): {get_ret}', '    {',
                f'        return {get_map}($this->object(', '            Transport::METHOD_GET,', f'            {path_expr(sc, True)},'] + gq_arg + ['        ));', '    }', '']
    if 'create' in methods:
        sc = scope('POST', coll)
        out += ['    /**', f'     * Create {cfg["article"]} {entity} (operationId `{spec_op("POST", coll)}`, body `{bodies.get("POST", "?")}`).', '     *',
                f'     * @param {inp}|array<string, mixed> ${cfg["body_arg"]}', '     *', '     * @throws KongApiException', '     */', opattr('POST', coll),
                f'    public function create({inp}|array ${cfg["body_arg"]}): {ret}', '    {',
                f'        return {mapone}($this->object(', '            Transport::METHOD_POST,', f'            {path_expr(sc, False)},',
                f'            body: ${cfg["body_arg"]},', '        ));', '    }', '']
    if 'update' in methods:
        sc = scope('PATCH', item)
        out += ['    /**', f'     * Update fields of {cfg["article"]} {entity} (operationId `{spec_op("PATCH", item)}`, PATCH, body `{bodies.get("PATCH", "?")}`).', '     *',
                f'     * @param {inp}|array<string, mixed> ${cfg["body_arg"]} only the fields to change', '     *', '     * @throws KongApiException', item_throw, '     */', opattr('PATCH', item),
                f'    public function update(string ${item_arg}, {inp}|array ${cfg["body_arg"]}): {ret}', '    {',
                f'        return {mapone}($this->object(', '            Transport::METHOD_PATCH,', f'            {path_expr(sc, True)},',
                f'            body: ${cfg["body_arg"]},', '        ));', '    }', '']
    if 'upsert' in methods:
        sc = scope('PUT', item)
        out += ['    /**', f'     * Create or replace {cfg["article"]} {entity} by {cfg["item_arg_doc"]} (operationId `{spec_op("PUT", item)}`, PUT, body `{bodies.get("PUT", "?")}`).', '     *',
                f'     * @param {inp}|array<string, mixed> ${cfg["body_arg"]}', '     *', '     * @throws KongApiException', item_throw, '     */', opattr('PUT', item),
                f'    public function upsert(string ${item_arg}, {inp}|array ${cfg["body_arg"]}): {ret}', '    {',
                f'        return {mapone}($this->object(', '            Transport::METHOD_PUT,', f'            {path_expr(sc, True)},',
                f'            body: ${cfg["body_arg"]},', '        ));', '    }', '']
    if 'delete' in methods:
        sc = scope('DELETE', item)
        out += ['    /**', f'     * Delete {cfg["article"]} {entity} (operationId `{spec_op("DELETE", item)}`). Kong answers 204 whether or not it existed.', '     *',
                '     * @throws KongApiException', item_throw, '     */', opattr('DELETE', item),
                f'    public function delete(string ${item_arg}): void', '    {',
                f'        $this->none(Transport::METHOD_DELETE, {path_expr(sc, True)});', '    }', '']
    for acc in cfg.get('accessors', []):
        out += accessor_lines(acc) + ['']
    if out and out[-1] == '':
        out.pop()
    lines += out + ['}', '']
    path = 'src/Resources/' + ('Nested/' if cfg['nested'] else '') + cls + '.php'
    write(path, '\n'.join(lines))
    return path


def accessor_lines(acc):
    """acc: dict(name, cls, arg, arg_doc, path_doc, segment_expr)"""
    return ['    /**', f"     * {acc['doc']}: `{acc['path_doc']}`.", '     *',
            f"     * @throws InvalidArgumentException when ${acc['arg']} is empty", '     */',
            f"    public function {acc['name']}(string ${acc['arg']}): {acc['cls']}", '    {',
            f"        if (${acc['arg']} === '') {{",
            f"            throw new InvalidArgumentException('{acc['arg_doc']} must not be empty.');", '        }', '',
            f"        return new {acc['cls']}($this->transport, [...$this->parent, {acc['segment_expr']}, ${acc['arg']}]);", '    }']
