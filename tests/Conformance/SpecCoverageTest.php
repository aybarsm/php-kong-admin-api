<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Tests\Support\SourceClasses;
use Aybarsm\Kong\AdminApi\Tests\Support\Spec;

/**
 * Flip to true at the end of Phase 4: from then on every spec operation must be implemented or blocked.
 */
function specCoverageEnforced(): bool
{
    return false;
}

/**
 * @return array<string, true> "METHOD path" claimed by #[Operation] attributes, workspace twins included
 */
function claimedOperations(): array
{
    $claimed = [];
    foreach (SourceClasses::in('Resources') as $class) {
        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            foreach ($method->getAttributes(Operation::class) as $attribute) {
                $operation = $attribute->newInstance();
                $claimed[$operation->method . ' ' . $operation->path] = true;
                if ($operation->scope === OperationScope::Both) {
                    $claimed[$operation->method . ' /{workspace}' . $operation->path] = true;
                }
            }
        }
    }

    return $claimed;
}

/**
 * @return list<string> "METHOD path" entries between the blocked markers in docs/spec-notes.md
 */
function blockedOperations(): array
{
    $notes = file_get_contents(dirname(__DIR__, 2) . '/docs/spec-notes.md');
    if ($notes === false || preg_match('/<!-- blocked:start -->(.*?)<!-- blocked:end -->/s', $notes, $match) !== 1) {
        throw new RuntimeException('docs/spec-notes.md has no blocked-operations block');
    }

    return array_values(array_filter(array_map('trim', explode("\n", $match[1])), static fn (string $line): bool => $line !== ''));
}

it('lists only real spec operations as blocked, none of them implemented', function (): void {
    $spec = Spec::operations();
    $claimed = claimedOperations();

    foreach (blockedOperations() as $blocked) {
        expect(array_key_exists($blocked, $spec))->toBeTrue("blocked $blocked is not in the spec")
            ->and(array_key_exists($blocked, $claimed))->toBeFalse("blocked $blocked is implemented");
    }
});

it('implements every spec operation or lists it as blocked', function (): void {
    $remaining = array_diff(array_keys(Spec::operations()), array_keys(claimedOperations()), blockedOperations());

    expect(array_values($remaining))->toBe([]);
})->skip(!specCoverageEnforced(), 'Spec coverage is enforced from the end of Phase 4.');
