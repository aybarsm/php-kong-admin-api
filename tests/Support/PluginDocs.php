<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Tests\Support;

use RuntimeException;

/**
 * Read-only access to the plugin docs (`resources/kong-admin-api/plugins/{Category}/{plugin}.md`) and the
 * blocked list in `docs/plugin-notes.md`, for tests.
 */
final class PluginDocs
{
    /** @var array<string, string> raw doc text by doc path (`Category/plugin.md`) */
    private static array $texts = [];

    public static function directory(): string
    {
        return dirname(__DIR__, 2) . '/resources/kong-admin-api/plugins';
    }

    /**
     * Every doc, as paths relative to the plugin docs directory.
     *
     * @return list<string>
     */
    public static function all(): array
    {
        $paths = glob(self::directory() . '/*/*.md');
        if ($paths === false || $paths === []) {
            throw new RuntimeException('No plugin docs found: ' . self::directory() . '/{Category}/{plugin}.md');
        }
        $docs = array_map(static fn (string $path): string => substr($path, strlen(self::directory()) + 1), $paths);
        sort($docs);

        return $docs;
    }

    /**
     * Docs listed between the `blocked-plugins` markers of docs/plugin-notes.md.
     *
     * @return list<string>
     */
    public static function blocked(): array
    {
        $notes = (string) file_get_contents(dirname(__DIR__, 2) . '/docs/plugin-notes.md');
        if (preg_match('/<!-- blocked-plugins:start -->(.*?)<!-- blocked-plugins:end -->/s', $notes, $section) !== 1) {
            throw new RuntimeException('docs/plugin-notes.md has no blocked-plugins markers');
        }
        preg_match_all('/^- `([^`]+\.md)`/m', $section[1], $matches);

        return $matches[1];
    }

    /**
     * The plugin's wire name: the slug of the front-matter `url` (`/plugins/{name}/reference/`).
     */
    public static function name(string $doc): string
    {
        if (preg_match('#^url: "?/plugins/([^/"]+)/reference/"?\s*$#m', self::frontMatter($doc), $match) !== 1) {
            throw new RuntimeException('No /plugins/{name}/reference/ url in ' . $doc);
        }

        return $match[1];
    }

    /**
     * The doc's JSON Schema block.
     *
     * @return array<string, mixed>
     */
    public static function schema(string $doc): array
    {
        if (preg_match_all('/^```json\n(.*?)\n^```\s*$/sm', self::text($doc), $blocks) !== 1) {
            throw new RuntimeException('Expected exactly one json block in ' . $doc);
        }
        $decoded = json_decode($blocks[1][0], true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($decoded)) {
            throw new RuntimeException('The json block is not an object in ' . $doc);
        }

        return Spec::stringKeys($decoded, $doc);
    }

    /**
     * A node of the doc schema by JSON pointer (`#`, `#/properties/config/properties/redis`, `…/items`).
     *
     * @return array<string, mixed>
     */
    public static function node(string $doc, string $pointer): array
    {
        $node = self::schema($doc);
        if ($pointer === '#') {
            return $node;
        }
        foreach (explode('/', substr($pointer, 2)) as $segment) {
            $next = $node[str_replace(['~1', '~0'], ['/', '~'], $segment)] ?? null;
            if (!is_array($next)) {
                throw new RuntimeException("Doc node not found: $doc $pointer");
            }
            $node = Spec::stringKeys($next, "$doc $pointer");
        }

        return $node;
    }

    /**
     * Property names of an object node, in doc order.
     *
     * @param array<string, mixed> $node
     *
     * @return list<string>
     */
    public static function properties(array $node): array
    {
        $properties = $node['properties'] ?? [];

        return is_array($properties) ? array_keys(Spec::stringKeys($properties, 'properties')) : [];
    }

    /**
     * Names of the `required` properties of an object node.
     *
     * @param array<string, mixed> $node
     *
     * @return list<string>
     */
    public static function required(array $node): array
    {
        $required = $node['required'] ?? [];

        return is_array($required) ? array_values(array_filter($required, is_string(...))) : [];
    }

    /**
     * Compares PHP and doc property names ignoring case and separators (`cloud_authentication` = `cloudAuthentication`).
     */
    public static function wireKey(string $name): string
    {
        return strtolower((string) preg_replace('/[^A-Za-z0-9]/', '', $name));
    }

    private static function frontMatter(string $doc): string
    {
        if (preg_match('/^---\n(.*?)\n---\n/s', self::text($doc), $match) !== 1) {
            throw new RuntimeException('No front matter in ' . $doc);
        }

        return $match[1];
    }

    private static function text(string $doc): string
    {
        if (!isset(self::$texts[$doc])) {
            $path = self::directory() . '/' . $doc;
            $text = is_file($path) ? file_get_contents($path) : false;
            if ($text === false || $text === '') {
                throw new RuntimeException('Missing plugin doc: ' . $path);
            }
            self::$texts[$doc] = $text;
        }

        return self::$texts[$doc];
    }
}
