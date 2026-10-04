<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Enums\Protocol;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginConfig;
use Aybarsm\Kong\AdminApi\Plugins\PluginRegistry;
use Aybarsm\Kong\AdminApi\Plugins\TypedPluginInput;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\PluginDocs;
use Aybarsm\Kong\AdminApi\Tests\Support\SourceClasses;
use Aybarsm\Kong\AdminApi\Tests\Support\Spec;

/*
 * Every generated plugin class against its doc in resources/kong-admin-api/plugins/ (see docs/plugin-notes.md).
 * The checks are generic, so a newly generated plugin is covered without a new test file. No covers(): like
 * ModelRoundTripTest, this file's coverage must count for every generated class it exercises.
 */

const PLUGIN_ROOT_FIELDS = ['config', 'consumer', 'consumer_group', 'expressions', 'protocols', 'route', 'service'];

/**
 * Every class and enum under src/Plugins carrying #[PluginSchema], with its attribute.
 *
 * @return list<array{ReflectionClass<object>, PluginSchema}>
 */
function pluginSchemaClasses(): array
{
    $classes = [];
    foreach (SourceClasses::in('Plugins') as $class) {
        foreach ($class->getAttributes(PluginSchema::class) as $attribute) {
            $classes[] = [$class, $attribute->newInstance()];
        }
    }

    return $classes;
}

/**
 * The `{Plugin}Config` class of every implemented doc.
 *
 * @return array<string, class-string<PluginConfig>> by doc path
 */
function pluginConfigClasses(): array
{
    $configs = [];
    foreach (pluginSchemaClasses() as [$class, $schema]) {
        if ($class->implementsInterface(PluginConfig::class)) {
            /** @var class-string<PluginConfig> $name */
            $name = $class->getName();
            $configs[$schema->doc] = $name;
        }
    }
    ksort($configs);

    return $configs;
}

/**
 * The generated fixture of a plugin's `config` (tests/Fixtures/Plugins/{Category}/{plugin}.json).
 *
 * @return array<string, mixed>
 */
function pluginFixture(string $doc): array
{
    return Fixture::get('Plugins/' . substr($doc, 0, -3));
}

/**
 * Builds $class from the public properties of $model by constructor parameter name.
 *
 * @param array<string, mixed> $extra
 */
function pluginInstance(string $class, object $model, array $extra = []): object
{
    /** @var class-string $class */
    $reflection = new ReflectionClass($class);
    $values = get_object_vars($model);
    $arguments = [];
    foreach ($reflection->getConstructor()?->getParameters() ?? [] as $parameter) {
        $arguments[$parameter->getName()] = $values[$parameter->getName()] ?? null;
    }

    return $reflection->newInstanceArgs([...$arguments, ...$extra]);
}

/**
 * The part of a config fixture a nested DTO reads: follows `properties/<name>`, `items` (first element) and
 * `additionalProperties` (first value) of the class's pointer.
 *
 * @param array<string, mixed> $config
 *
 * @return array<string, mixed>
 */
function pluginFixtureAt(array $config, string $pointer): array
{
    $node = $config;
    $segments = explode('/', substr($pointer, strlen('#/properties/config/')));
    foreach ($segments as $index => $segment) {
        if ($segment === 'properties' || ($index === 0 && $segment === '')) {
            continue;
        }
        $next = match ($segment) {
            'items' => array_is_list($node) ? ($node[0] ?? null) : null,
            'additionalProperties' => array_values($node)[0] ?? null,
            default => $node[$segment] ?? null,
        };
        if (!is_array($next)) {
            throw new RuntimeException("No fixture data at $pointer");
        }
        $node = $next;
    }

    return Spec::stringKeys($node, $pointer);
}

function pluginClassName(string $doc): string
{
    $slug = pathinfo($doc, PATHINFO_FILENAME);

    return 'Aybarsm\\Kong\\AdminApi\\Plugins\\' . dirname($doc) . '\\'
        . str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $slug)));
}

it('implements every plugin doc that is not blocked, and nothing else', function (): void {
    $blocked = PluginDocs::blocked();

    expect(array_values(array_diff($blocked, PluginDocs::all())))->toBe([])
        ->and(array_keys(pluginConfigClasses()))->toBe(array_values(array_diff(PluginDocs::all(), $blocked)));
});

it('blocks only docs that duplicate another plugin doc', function (): void {
    foreach (PluginDocs::blocked() as $doc) {
        $config = PluginDocs::node($doc, '#/properties/config');
        $twins = array_filter(
            PluginDocs::all(),
            static fn (string $other): bool => $other !== $doc && PluginDocs::node($other, '#/properties/config') === $config,
        );

        expect($twins)->not->toBeEmpty("$doc is blocked but duplicates no other doc: unblock it")
            ->and(PluginDocs::name($doc))->not->toBe(pathinfo($doc, PATHINFO_FILENAME));
    }
});

it('places, names and registers every plugin after its doc', function (): void {
    $registered = [];
    foreach (pluginConfigClasses() as $doc => $config) {
        $namespace = pluginClassName($doc);
        $short = substr($namespace, (int) strrpos($namespace, '\\') + 1);
        $name = PluginDocs::name($doc);

        expect($config)->toBe("$namespace\\{$short}Config");
        foreach (['Config', 'ConfigInput', 'Input'] as $suffix) {
            expect(constant("$namespace\\$short$suffix::NAME"))->toBe($name, "$short$suffix::NAME");
        }
        expect(is_subclass_of("$namespace\\{$short}Input", TypedPluginInput::class))->toBeTrue()
            ->and(is_subclass_of("$namespace\\{$short}ConfigInput", Input::class))->toBeTrue()
            ->and(PluginRegistry::has($name))->toBeTrue();
        $registered[$name] = $config;
    }
    ksort($registered);
    $actual = PluginRegistry::configs();
    ksort($actual);

    expect($actual)->toBe($registered)
        ->and(PluginRegistry::has('not-a-documented-plugin'))->toBeFalse();
});

it('mirrors the doc properties and required-ness in every config DTO', function (): void {
    $checked = 0;
    foreach (pluginSchemaClasses() as [$class, $schema]) {
        if ($class->isEnum() || $class->implementsInterface(TypedPluginInput::class)) {
            continue;
        }
        $node = PluginDocs::node($schema->doc, $schema->pointer);
        $required = array_map(PluginDocs::wireKey(...), PluginDocs::required($node));
        $isInput = $class->implementsInterface(Input::class);
        $short = $class->getShortName();

        $actual = [];
        foreach ($class->getConstructor()?->getParameters() ?? [] as $parameter) {
            $wire = PluginDocs::wireKey($parameter->getName());
            $actual[] = $wire;
            $nullable = $parameter->getType()?->allowsNull() ?? true;
            $optional = $isInput || !in_array($wire, $required, true);

            expect($nullable)->toBe($optional, "$short::\${$parameter->getName()} nullability");
        }

        expect($actual)->toEqualCanonicalizing(array_map(PluginDocs::wireKey(...), PluginDocs::properties($node)), "$short vs {$schema->doc} {$schema->pointer}")
            ->and($class->implementsInterface($isInput ? Input::class : Model::class))->toBeTrue();
        $checked++;
    }

    expect($checked)->toBeGreaterThan(count(pluginConfigClasses()) * 2);
});

it('types each plugin input from the doc scopes and the spec Plugin fields', function (): void {
    $specFields = PluginDocs::properties(Spec::schema('Plugin'));
    foreach (pluginSchemaClasses() as [$class, $schema]) {
        if (!$class->implementsInterface(TypedPluginInput::class)) {
            continue;
        }
        $docFields = PluginDocs::properties(PluginDocs::schema($schema->doc));
        $expected = array_values(array_diff($specFields, ['name'], array_diff(PLUGIN_ROOT_FIELDS, $docFields)));
        $parameters = $class->getConstructor()?->getParameters() ?? [];

        expect(array_values(array_diff($docFields, PLUGIN_ROOT_FIELDS)))->toBe([], "{$schema->doc} has unknown root fields")
            ->and($schema->pointer)->toBe('#')
            ->and(array_map(static fn (ReflectionParameter $p): string => PluginDocs::wireKey($p->getName()), $parameters))
            ->toEqualCanonicalizing(array_map(PluginDocs::wireKey(...), $expected), $class->getShortName());
        foreach ($parameters as $parameter) {
            expect($parameter->getType()?->allowsNull())->toBeTrue();
        }
    }
});

it('backs every plugin enum with the doc values, in doc order', function (): void {
    $checked = 0;
    foreach (pluginSchemaClasses() as [$class, $schema]) {
        if (!$class->isEnum()) {
            continue;
        }
        $node = PluginDocs::node($schema->doc, $schema->pointer);
        /** @var class-string<BackedEnum> $enum */
        $enum = $class->getName();
        $backing = (new ReflectionEnum($enum))->getBackingType();

        expect(array_map(static fn (BackedEnum $case): int|string => $case->value, $enum::cases()))->toBe($node['enum'] ?? null)
            ->and($backing instanceof ReflectionNamedType ? $backing->getName() : null)
            ->toBe(($node['type'] ?? null) === 'integer' ? 'int' : 'string');
        $checked++;
    }

    expect($checked)->toBeGreaterThan(100);
});

it('keeps every plugin\'s protocols within the spec Protocol enum', function (): void {
    $known = array_map(static fn (Protocol $case): string => $case->value, Protocol::cases());
    foreach (array_keys(pluginConfigClasses()) as $doc) {
        $protocols = PluginDocs::node($doc, '#/properties/protocols/items')['enum'] ?? null;
        $values = is_array($protocols) ? array_filter($protocols, is_string(...)) : [];

        expect($protocols)->toBeArray()
            ->and($values)->toHaveCount(is_array($protocols) ? count($protocols) : 0)
            ->and(array_values(array_diff($values, $known)))->toBe([], $doc);
    }
});

it('round-trips every plugin config fixture and covers every doc property', function (string $doc, string $config): void {
    /** @var class-string<PluginConfig> $config */
    $data = pluginFixture($doc);
    $properties = PluginDocs::properties(PluginDocs::node($doc, '#/properties/config'));

    expect(array_keys($data))->toEqualCanonicalizing($properties)
        ->and($config::fromArray($data)->toArray())->toEqual($data);
})->with(function (): array {
    $rows = [];
    foreach (pluginConfigClasses() as $doc => $config) {
        $rows[$doc] = [$doc, $config];
    }

    return $rows;
});

it('serialises every config input and plugin input exactly like the config', function (string $doc, string $config): void {
    /** @var class-string<PluginConfig> $config */
    $data = pluginFixture($doc);
    $model = $config::fromArray($data);
    $configInput = pluginInstance($config . 'Input', $model);
    $name = PluginDocs::name($doc);
    $scopes = array_intersect(['consumer', 'consumer_group', 'route', 'service'], PluginDocs::properties(PluginDocs::schema($doc)));
    $protocol = PluginDocs::node($doc, '#/properties/protocols/items')['enum'] ?? [];
    $protocol = Protocol::from(is_array($protocol) && is_string($protocol[0] ?? null) ? $protocol[0] : 'http');

    $arguments = ['config' => $configInput, 'enabled' => false, 'protocols' => [$protocol], 'tags' => ['edge']];
    $expected = ['name' => $name, 'config' => $data, 'enabled' => false, 'protocols' => [$protocol->value], 'tags' => ['edge']];
    foreach ($scopes as $scope) {
        $arguments[lcfirst(str_replace('_', '', ucwords($scope, '_')))] = "$scope-id";
        $expected[$scope] = ['id' => "$scope-id"];
    }
    /** @var class-string<TypedPluginInput> $pluginInputClass */
    $pluginInputClass = substr($config, 0, -strlen('Config')) . 'Input';
    $pluginInput = (new ReflectionClass($pluginInputClass))->newInstanceArgs($arguments);
    $raw = (new ReflectionClass($pluginInputClass))->newInstanceArgs(['config' => ['custom' => null]]);

    expect($configInput)->toBeInstanceOf(Input::class)
        ->and($configInput instanceof Input ? $configInput->toArray() : null)->toEqual($data)
        ->and($pluginInput->toArray())->toEqual($expected)
        ->and($raw->toArray())->toBe(['name' => $name, 'config' => ['custom' => null]]);
})->with(function (): array {
    $rows = [];
    foreach (pluginConfigClasses() as $doc => $config) {
        $rows[$doc] = [$doc, $config];
    }

    return $rows;
});

it('reads the typed config of a returned plugin and rejects another plugin', function (string $doc, string $config): void {
    /** @var class-string<PluginConfig> $config */
    $data = pluginFixture($doc);
    $name = PluginDocs::name($doc);

    expect($config::fromPlugin(new Plugin(name: $name, config: $data))->toArray())->toEqual($data)
        ->and(PluginRegistry::config(new Plugin(name: $name, config: $data)))->toBeInstanceOf($config)
        ->and(fn () => $config::fromPlugin(new Plugin(name: 'other', config: $data)))
        ->toThrow(InvalidArgumentException::class, sprintf('Expected a "%s" plugin, got "other".', $name));
})->with(function (): array {
    $rows = [];
    foreach (pluginConfigClasses() as $doc => $config) {
        $rows[$doc] = [$doc, $config];
    }

    return $rows;
});

it('redacts every x-encrypted plugin field in __debugInfo', function (): void {
    $checked = 0;
    foreach (pluginSchemaClasses() as [$class, $schema]) {
        if ($class->isEnum() || $class->implementsInterface(TypedPluginInput::class)) {
            continue;
        }
        $node = PluginDocs::node($schema->doc, $schema->pointer);
        $properties = $node['properties'] ?? [];
        $encrypted = [];
        foreach (is_array($properties) ? $properties : [] as $wire => $definition) {
            if (is_string($wire) && is_array($definition) && ($definition['x-encrypted'] ?? false) === true) {
                $encrypted[$wire] = lcfirst(str_replace('_', '', ucwords($wire, '_')));
            }
        }
        $constant = $class->getReflectionConstant('ENCRYPTED');
        if ($encrypted === []) {
            expect($constant)->toBeFalse($class->getShortName() . ' declares ENCRYPTED without encrypted properties');

            continue;
        }

        expect($constant === false ? null : $constant->getValue())->toEqualCanonicalizing(array_values($encrypted));

        $data = pluginFixtureAt(pluginFixture($schema->doc), $schema->pointer);
        foreach (array_keys($encrypted) as $wire) {
            $data[$wire] = 'top-secret-value';
        }
        /** @var class-string<Model> $output */
        $output = $class->implementsInterface(Input::class) ? substr($class->getName(), 0, -strlen('Input')) : $class->getName();
        $instance = $output::fromArray($data);
        if ($class->implementsInterface(Input::class)) {
            $instance = pluginInstance($class->getName(), $instance);
        }

        expect(print_r($instance, true))->not->toContain('top-secret-value')->toContain('***');
        $checked++;
    }

    expect($checked)->toBeGreaterThan(10);
});

it('lists every implemented plugin in the README table', function (): void {
    $readme = (string) file_get_contents(dirname(__DIR__, 2) . '/README.md');
    preg_match('/<!-- plugins:start -->(.*?)<!-- plugins:end -->/s', $readme, $table);
    preg_match_all('/^\| (?!Category )[^|]+\| [^|]*\(`([a-z0-9-]+)`\) \|/m', $table[1] ?? '', $names);
    $listed = $names[1];
    sort($listed);
    $registered = array_keys(PluginRegistry::configs());
    sort($registered);

    expect($listed)->toBe($registered);
});
