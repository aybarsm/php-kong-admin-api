<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Tests\Support\Fixture;
use Aybarsm\Kong\AdminApi\Tests\Support\SourceClasses;
use Aybarsm\Kong\AdminApi\Tests\Support\Spec;

/*
 * Generic checks over every #[Schema] DTO, driven by tests/Fixtures/{snake_case_class}.json.
 * A fixture holds every property of the schema (writeOnly excluded), so a round trip exercises
 * every reader and writer, nested DTOs included.
 */

function fixtureName(string $shortClass): string
{
    return strtolower((string) preg_replace('/(?<=[a-z0-9])(?=[A-Z])|(?<=[A-Z])(?=[A-Z][a-z])/', '_', $shortClass));
}

/**
 * @return array<string, array{class-string<Model>, string}> output DTOs with a fixture, keyed by class short name
 */
function fixtureBackedModels(): array
{
    $models = [];
    foreach (SourceClasses::in('Models') as $class) {
        $attributes = $class->getAttributes(Schema::class);
        if ($attributes === [] || !$class->implementsInterface(Model::class)) {
            continue;
        }
        $name = fixtureName($class->getShortName());
        if (is_file(dirname(__DIR__) . '/Fixtures/' . $name . '.json')) {
            /** @var class-string<Model> $fqcn */
            $fqcn = $class->getName();
            $models[$class->getShortName()] = [$fqcn, $attributes[0]->newInstance()->name];
        }
    }

    return $models;
}

it('has a complete fixture for every top-level entity DTO', function (): void {
    $missing = [];
    foreach (SourceClasses::in('Models') as $class) {
        $attributes = $class->getAttributes(Schema::class);
        $isTopLevel = $attributes !== [] && !str_contains($attributes[0]->newInstance()->name, '/properties/');
        if ($isTopLevel && $class->implementsInterface(Model::class) && !isset(fixtureBackedModels()[$class->getShortName()])) {
            $missing[] = $class->getShortName();
        }
    }

    expect($missing)->toBe([]);
});

it('round-trips every fixture and covers every spec property', function (string $class, string $schema, string $fixture): void {
    /** @var class-string<Model> $class */
    $data = Fixture::get($fixture);
    $properties = Spec::schema($schema)['properties'] ?? [];
    expect($properties)->toBeArray();
    /** @var array<string, array<string, mixed>> $properties */
    $readable = array_keys(array_filter($properties, static fn (array $p): bool => ($p['writeOnly'] ?? false) !== true));

    expect(array_keys($data))->toEqualCanonicalizing($readable)
        ->and($class::fromArray($data)->toArray())->toEqual($data);
})->with(function (): array {
    $rows = [];
    foreach (fixtureBackedModels() as $short => [$class, $schema]) {
        $rows[$short] = [$class, $schema, fixtureName($short)];
    }

    return $rows;
});

it('serialises every input exactly like its output DTO', function (): void {
    $outputs = [];
    foreach (fixtureBackedModels() as [$class, $schema]) {
        $outputs[$schema] = $class;
    }

    $checked = 0;
    foreach (SourceClasses::in('Models') as $class) {
        $attributes = $class->getAttributes(Schema::class);
        $output = $attributes === [] ? null : ($outputs[$attributes[0]->newInstance()->name] ?? null);
        if ($output === null || !$class->implementsInterface(Input::class)) {
            continue;
        }

        $model = $output::fromArray(Fixture::get(fixtureName((new ReflectionClass($output))->getShortName())));
        $values = get_object_vars($model);
        $arguments = [];
        foreach ($class->getConstructor()?->getParameters() ?? [] as $parameter) {
            $arguments[$parameter->getName()] = $values[$parameter->getName()] ?? null;
        }
        $input = $class->newInstanceArgs($arguments);
        expect($input)->toBeInstanceOf(Input::class);
        /** @var Input $input */
        expect($input->toArray())->toEqual($model->toArray(), $class->getShortName());
        $checked++;
    }

    expect($checked)->toBeGreaterThan(10);
});

/**
 * Secrets the spec doesn't mark `x-encrypted`, redacted anyway (spec-notes Q18). Pinned here so the
 * generator's SENSITIVE tables can't drift silently.
 *
 * @return array<string, list<string>> class short name => camelCase properties
 */
function reviewedSecrets(): array
{
    return [
        'KeyAuth' => ['key'],
        'KeyAuthInput' => ['key'],
        'Jwt' => ['secret'],
        'JwtInput' => ['secret'],
        'RbacUserInput' => ['userToken'],
        'AdminRegistrationInput' => ['password', 'token'],
        'AdminPasswordResetInput' => ['password', 'token'],
        'LicenseReportLicense' => ['licenseKey'],
        'EventHookConfig' => ['secret'],
        'WebhookInput' => ['configSecret'],
        'Keyring' => ['key'],
        'KeyringInput' => ['key'],
        'KeyringImportInput' => ['key'],
        'KeyringVaultSyncInput' => ['token'],
        'KeyringImportResult' => ['password'],
    ];
}

it('redacts every x-encrypted property and every reviewed secret in __debugInfo', function (): void {
    $checked = 0;
    foreach (SourceClasses::in('Models') as $class) {
        $attributes = $class->getAttributes(Schema::class);
        if ($attributes === []) {
            continue;
        }
        $properties = Spec::schema($attributes[0]->newInstance()->name)['properties'] ?? [];
        expect($properties)->toBeArray();
        /** @var array<string, array<string, mixed>> $properties */
        $encrypted = reviewedSecrets()[$class->getShortName()] ?? [];
        foreach ($properties as $wire => $definition) {
            if (($definition['x-encrypted'] ?? false) === true) {
                $encrypted[] = lcfirst(str_replace('_', '', ucwords($wire, '_')));
            }
        }
        $constant = $class->getReflectionConstant('ENCRYPTED');

        if ($encrypted === []) {
            expect($constant)->toBeFalse($class->getShortName() . ' declares ENCRYPTED without encrypted properties');

            continue;
        }

        expect($constant)->not->toBeFalse($class->getShortName() . ' must declare ENCRYPTED')
            ->and($constant === false ? null : $constant->getValue())->toEqualCanonicalizing($encrypted);

        $arguments = [];
        foreach ($class->getConstructor()?->getParameters() ?? [] as $parameter) {
            $type = $parameter->getType();
            $arguments[$parameter->getName()] = in_array($parameter->getName(), $encrypted, true)
                ? 'top-secret-value'
                : ($parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : ($type instanceof ReflectionNamedType && $type->getName() === 'string' ? 'x' : null));
        }
        $instance = $class->newInstanceArgs($arguments);

        expect(print_r($instance, true))->not->toContain('top-secret-value')->toContain('***');
        $checked++;
    }

    expect($checked)->toBeGreaterThan(0);
});

it('lists only existing properties as reviewed secrets', function (): void {
    foreach (reviewedSecrets() as $short => $properties) {
        $fqcn = 'Aybarsm\\Kong\\AdminApi\\Models\\' . $short;
        expect(class_exists($fqcn))->toBeTrue($fqcn);
        /** @var class-string $fqcn */
        $class = new ReflectionClass($fqcn);

        foreach ($properties as $property) {
            expect($class->hasProperty($property))->toBeTrue("$short::\\$$property");
        }
    }
});
