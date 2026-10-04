<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Tests\Support;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use SplFileInfo;

/**
 * Discovers package classes under src/ for reflection-based conformance tests.
 */
final class SourceClasses
{
    public const string ROOT_NAMESPACE = 'Aybarsm\\Kong\\AdminApi\\';

    /**
     * @return list<ReflectionClass<object>>
     */
    public static function in(string $subdirectory = ''): array
    {
        $root = dirname(__DIR__, 2) . '/src';
        $base = rtrim($root . '/' . $subdirectory, '/');
        if (!is_dir($base)) {
            return [];
        }

        $classes = [];
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, RecursiveDirectoryIterator::SKIP_DOTS));
        foreach ($files as $file) {
            if (!$file instanceof SplFileInfo || $file->getExtension() !== 'php') {
                continue;
            }
            $relative = substr($file->getPathname(), strlen($root) + 1, -4);
            $class = self::ROOT_NAMESPACE . str_replace('/', '\\', $relative);
            if (class_exists($class) || interface_exists($class) || enum_exists($class)) {
                $classes[] = new ReflectionClass($class);
            }
        }

        usort($classes, static fn (ReflectionClass $a, ReflectionClass $b): int => strcmp($a->getName(), $b->getName()));

        return $classes;
    }

    /**
     * @return list<string> source files under src/
     */
    public static function files(): array
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(dirname(__DIR__, 2) . '/src', RecursiveDirectoryIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            if ($file instanceof SplFileInfo && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }
        sort($files);

        return $files;
    }
}
