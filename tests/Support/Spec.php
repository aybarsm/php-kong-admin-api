<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Tests\Support;

use Aybarsm\Kong\AdminApi\KongSpec;
use RuntimeException;

/**
 * Read-only access to the canonical Kong Admin API spec for tests.
 */
final class Spec
{
    public const array HTTP_METHODS = ['get', 'post', 'put', 'patch', 'delete', 'head', 'options'];

    /** @var array<string, mixed>|null */
    private static ?array $document = null;

    public static function path(): string
    {
        return dirname(__DIR__, 2) . '/resources/kong-admin-api/' . KongSpec::SPEC_FILE;
    }

    /**
     * @return array<string, mixed>
     */
    public static function document(): array
    {
        if (self::$document !== null) {
            return self::$document;
        }

        $path = self::path();
        $raw = is_file($path) ? file_get_contents($path) : false;
        if ($raw === false || $raw === '') {
            throw new RuntimeException('Missing Kong spec: ' . $path);
        }

        $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($decoded)) {
            throw new RuntimeException('Kong spec is not a JSON object: ' . $path);
        }

        return self::$document = self::stringKeys($decoded, 'document');
    }

    /**
     * @return array<string, mixed>
     */
    public static function section(string ...$keys): array
    {
        // Intermediate nodes may have integer keys (e.g. response status codes such as "201").
        $node = self::document();
        foreach ($keys as $key) {
            $next = $node[$key] ?? null;
            if (!is_array($next)) {
                throw new RuntimeException('Spec node not found: ' . implode('.', $keys));
            }
            $node = $next;
        }

        return self::stringKeys($node, implode('.', $keys));
    }

    public static function infoVersion(): string
    {
        $version = self::section('info')['version'] ?? null;
        if (!is_string($version)) {
            throw new RuntimeException('Spec info.version missing');
        }

        return $version;
    }

    /**
     * Every operation in the spec, keyed "METHOD path".
     *
     * @return array<string, string> operationId by "METHOD path"
     */
    public static function operations(): array
    {
        $operations = [];
        foreach (self::section('paths') as $path => $item) {
            if (!is_array($item)) {
                continue;
            }
            foreach ($item as $method => $operation) {
                if (!is_string($method) || !in_array($method, self::HTTP_METHODS, true) || !is_array($operation)) {
                    continue;
                }
                $operationId = $operation['operationId'] ?? '';
                $operations[strtoupper($method) . ' ' . $path] = is_string($operationId) ? $operationId : '';
            }
        }

        return $operations;
    }

    /**
     * A component schema by name, or any spec node by JSON pointer (`#/...`); array `items` are
     * followed automatically when a pointer segment lands on an array schema.
     *
     * @return array<string, mixed>
     */
    public static function schema(string $nameOrPointer): array
    {
        if (!str_starts_with($nameOrPointer, '#/')) {
            return self::section('components', 'schemas', $nameOrPointer);
        }

        $segments = array_map(
            static fn (string $segment): string => str_replace(['~1', '~0'], ['/', '~'], $segment),
            explode('/', substr($nameOrPointer, 2)),
        );
        $node = self::section(...$segments);
        if (($node['type'] ?? null) === 'array' && is_array($node['items'] ?? null)) {
            $node = self::stringKeys($node['items'], $nameOrPointer . '/items');
        }

        return $node;
    }

    /**
     * @param array<mixed> $value
     *
     * @return array<string, mixed>
     */
    public static function stringKeys(array $value, string $context): array
    {
        $out = [];
        foreach ($value as $key => $item) {
            if (!is_string($key)) {
                throw new RuntimeException('Expected string keys at ' . $context);
            }
            $out[$key] = $item;
        }

        return $out;
    }
}
