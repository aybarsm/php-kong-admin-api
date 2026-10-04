<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Tests\Support\SourceClasses;
use Aybarsm\Kong\AdminApi\Tests\Support\Spec;

function snakeCase(string $name): string
{
    return strtolower((string) preg_replace('/(?<!^)[A-Z]/', '_$0', $name));
}

/**
 * @return array<string, array{ReflectionClass<object>, string}>
 */
function schemaClasses(): array
{
    $classes = [];
    foreach (SourceClasses::in('Models') as $class) {
        foreach ($class->getAttributes(Schema::class) as $attribute) {
            $classes[$class->getShortName()] = [$class, $attribute->newInstance()->name];
        }
    }

    return $classes;
}

it('finds schema-backed DTOs', function (): void {
    expect(schemaClasses())->not->toBeEmpty();
});

it('mirrors the spec schema properties, required-ness and writeOnly fields', function (): void {
    foreach (schemaClasses() as $short => [$class, $schemaName]) {
        $schema = Spec::schema($schemaName);
        $properties = is_array($schema['properties'] ?? null) ? Spec::stringKeys($schema['properties'], $schemaName) : [];
        // The spec has dotted property names (`config.limit`); PHP names map dots like underscores.
        $required = array_map(
            static fn (mixed $name): string => str_replace('.', '_', is_string($name) ? $name : ''),
            is_array($schema['required'] ?? null) ? $schema['required'] : [],
        );
        $isInput = $class->implementsInterface(Input::class);

        $expected = [];
        foreach ($properties as $name => $definition) {
            $flags = is_array($definition) ? $definition : [];
            if (!$isInput && ($flags['writeOnly'] ?? false) === true) {
                continue;
            }
            if ($isInput && ($flags['readOnly'] ?? false) === true) {
                continue;
            }
            $expected[] = str_replace('.', '_', $name);
        }

        $constructor = $class->getConstructor();
        expect($constructor)->not->toBeNull("$short has no constructor");
        /** @var ReflectionMethod $constructor */
        $actual = [];
        foreach ($constructor->getParameters() as $parameter) {
            $wire = snakeCase($parameter->getName());
            $actual[] = $wire;
            $type = $parameter->getType();
            $nullable = $type === null || $type->allowsNull();

            if ($isInput) {
                expect($nullable)->toBeTrue("$short::\${$parameter->getName()} must be nullable on an input");
            } elseif (in_array($wire, $required, true)) {
                expect($nullable)->toBeFalse("$short::\${$parameter->getName()} is required in $schemaName");
            } else {
                expect($nullable)->toBeTrue("$short::\${$parameter->getName()} is optional in $schemaName");
            }
        }

        expect($actual)->toEqualCanonicalizing($expected, "$short does not mirror $schemaName");
    }
});

it('implements the right contract for its role', function (): void {
    foreach (schemaClasses() as $short => [$class]) {
        $role = str_ends_with($short, 'Input') ? Input::class : Model::class;

        expect($class->implementsInterface($role))->toBeTrue("$short must implement $role");
    }
});
