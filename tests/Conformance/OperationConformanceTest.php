<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;
use Aybarsm\Kong\AdminApi\Tests\Support\SourceClasses;
use Aybarsm\Kong\AdminApi\Tests\Support\Spec;

/**
 * @return list<array{string, string, Operation}> [class::method, label, operation]
 */
function declaredOperations(): array
{
    $declared = [];
    foreach (SourceClasses::in('Resources') as $class) {
        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            foreach ($method->getAttributes(Operation::class) as $attribute) {
                $operation = $attribute->newInstance();
                $declared[] = [$class->getShortName() . '::' . $method->getName(), $operation->method . ' ' . $operation->path, $operation];
            }
        }
    }

    return $declared;
}

it('declares at least one operation', function (): void {
    expect(declaredOperations())->not->toBeEmpty();
});

it('maps every #[Operation] to a spec operation with the same operationId and scope', function (): void {
    $spec = Spec::operations();
    $problems = [];

    foreach (declaredOperations() as [$where, $key, $operation]) {
        $twin = $operation->method . ' /{workspace}' . $operation->path;

        if (!array_key_exists($key, $spec)) {
            $problems[] = "$where: $key is not in the spec";

            continue;
        }
        if ($spec[$key] !== $operation->operationId) {
            $problems[] = "$where: operationId {$operation->operationId} != spec {$spec[$key]}";
        }

        $problems = [...$problems, ...match ($operation->scope) {
            OperationScope::Both => array_key_exists($twin, $spec) ? [] : ["$where: scope Both but $twin is missing"],
            OperationScope::GlobalOnly => array_key_exists($twin, $spec) ? ["$where: scope GlobalOnly but $twin exists (use Both)"] : [],
            OperationScope::WorkspaceOnly => str_starts_with($operation->path, '/{workspace}/') ? [] : ["$where: scope WorkspaceOnly needs a /{workspace} path"],
        }];
    }

    expect($problems)->toBe([]);
});

it('gives every public resource method an #[Operation] unless it returns a nested resource', function (): void {
    $missing = [];
    foreach (SourceClasses::in('Resources') as $class) {
        if ($class->isAbstract()) {
            continue;
        }
        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isConstructor() || $method->getDeclaringClass()->getName() === AbstractResource::class) {
                continue;
            }
            $return = $method->getReturnType();
            $returnsResource = $return instanceof ReflectionNamedType
                && !$return->isBuiltin()
                && is_subclass_of($return->getName(), AbstractResource::class);
            if (!$returnsResource && $method->getAttributes(Operation::class) === []) {
                $missing[] = $class->getShortName() . '::' . $method->getName();
            }
        }
    }

    expect($missing)->toBe([]);
});
