<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\KongClient;
use Aybarsm\Kong\AdminApi\Tests\Support\SourceClasses;
use Psr\Http\Message\ResponseInterface;

/**
 * @return list<string> class names mentioned by a reflected type
 */
function typeNames(?ReflectionType $type): array
{
    return match (true) {
        $type instanceof ReflectionNamedType => [$type->getName()],
        $type instanceof ReflectionUnionType, $type instanceof ReflectionIntersectionType => array_merge(...array_map(typeNames(...), $type->getTypes())),
        default => [],
    };
}

it('exposes no ResponseInterface or Guzzle type in any public signature', function (): void {
    $leaks = [];
    foreach (SourceClasses::in() as $class) {
        if ($class->isInternal() || str_contains((string) $class->getDocComment(), '@internal')) {
            continue;
        }
        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->getDeclaringClass()->getName() !== $class->getName()) {
                continue;
            }
            $types = typeNames($method->getReturnType());
            foreach ($method->getParameters() as $parameter) {
                $types = [...$types, ...typeNames($parameter->getType())];
            }
            foreach ($types as $type) {
                if ($type === ResponseInterface::class || str_starts_with($type, 'GuzzleHttp\\')) {
                    $leaks[] = $class->getName() . '::' . $method->getName() . ' uses ' . $type;
                }
            }
        }
    }

    expect($leaks)->toBe([]);
});

it('lets the KongClient constructor accept only PSR interfaces and config', function (): void {
    $constructor = new ReflectionMethod(KongClient::class, '__construct');
    $types = [];
    foreach ($constructor->getParameters() as $parameter) {
        $types = [...$types, ...typeNames($parameter->getType())];
    }

    expect($types)->each(fn ($type) => $type->toMatch('/^(Psr\\\\Http\\\\|Aybarsm\\\\Kong\\\\AdminApi\\\\Config\\\\|null$)/'));
});

it('declares no native mixed type in src/', function (): void {
    $offenders = [];
    foreach (SourceClasses::files() as $file) {
        $code = (string) file_get_contents($file);
        if (preg_match_all('/(?:\(|,|\?|\|)\s*mixed\s+\$|\)\s*:\s*\??mixed\b|(?:public|protected|private|readonly)\s+\??mixed\s+\$/', $code, $matches) > 0) {
            $offenders[] = basename($file);
        }
    }

    expect($offenders)->toBe([]);
});
