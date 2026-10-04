<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Tests\Support;

use RuntimeException;

/**
 * Loads spec-shaped JSON payloads from tests/Fixtures.
 */
final class Fixture
{
    /**
     * @return array<string, mixed>
     */
    public static function get(string $name): array
    {
        $path = dirname(__DIR__) . '/Fixtures/' . $name . '.json';
        $raw = is_file($path) ? file_get_contents($path) : false;
        if ($raw === false) {
            throw new RuntimeException('Missing fixture ' . $path);
        }

        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($decoded)) {
            throw new RuntimeException('Fixture is not a JSON object: ' . $path);
        }

        return Spec::stringKeys($decoded, $name);
    }

    /**
     * The `example` the spec itself provides for a component schema.
     *
     * @return array<string, mixed>
     */
    public static function specExample(string $schema): array
    {
        $example = Spec::schema($schema)['example'] ?? null;
        if (!is_array($example)) {
            throw new RuntimeException('Spec schema has no example: ' . $schema);
        }

        return Spec::stringKeys($example, $schema . '.example');
    }
}
