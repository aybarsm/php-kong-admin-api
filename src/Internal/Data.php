<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Internal;

use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use BackedEnum;

/**
 * Typed readers used by DTO `fromArray()` implementations.
 *
 * Each reader either returns a value of the declared type or throws
 * UnexpectedResponseException naming the offending key. A key that is absent and a key that is
 * JSON `null` are both read as null by the `*OrNull` readers.
 *
 * @internal
 */
final class Data
{
    /** @codeCoverageIgnore Static holder; never instantiated. */
    private function __construct()
    {
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws UnexpectedResponseException
     */
    public static function string(array $data, string $key): string
    {
        return self::stringOrNull($data, $key) ?? throw self::missing($key);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws UnexpectedResponseException
     */
    public static function stringOrNull(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;
        if ($value === null || is_string($value)) {
            return $value;
        }

        throw self::invalid($key, 'string');
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws UnexpectedResponseException
     */
    public static function int(array $data, string $key): int
    {
        return self::intOrNull($data, $key) ?? throw self::missing($key);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws UnexpectedResponseException
     */
    public static function intOrNull(array $data, string $key): ?int
    {
        $value = $data[$key] ?? null;
        if ($value === null || is_int($value)) {
            return $value;
        }

        throw self::invalid($key, 'integer');
    }

    /**
     * Reads a spec `number`; JSON integers are widened to float.
     *
     * @param array<string, mixed> $data
     *
     * @throws UnexpectedResponseException
     */
    public static function floatOrNull(array $data, string $key): ?float
    {
        $value = $data[$key] ?? null;
        if ($value === null) {
            return null;
        }
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        throw self::invalid($key, 'number');
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws UnexpectedResponseException
     */
    public static function bool(array $data, string $key): bool
    {
        return self::boolOrNull($data, $key) ?? throw self::missing($key);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws UnexpectedResponseException
     */
    public static function boolOrNull(array $data, string $key): ?bool
    {
        $value = $data[$key] ?? null;
        if ($value === null || is_bool($value)) {
            return $value;
        }

        throw self::invalid($key, 'boolean');
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return list<string>|null
     *
     * @throws UnexpectedResponseException
     */
    public static function stringListOrNull(array $data, string $key): ?array
    {
        $items = self::listOrNull($data, $key);
        if ($items === null) {
            return null;
        }

        $out = [];
        foreach ($items as $item) {
            if (!is_string($item)) {
                throw self::invalid($key, 'list of strings');
            }
            $out[] = $item;
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return list<int>|null
     *
     * @throws UnexpectedResponseException
     */
    public static function intListOrNull(array $data, string $key): ?array
    {
        $items = self::listOrNull($data, $key);
        if ($items === null) {
            return null;
        }

        $out = [];
        foreach ($items as $item) {
            if (!is_int($item)) {
                throw self::invalid($key, 'list of integers');
            }
            $out[] = $item;
        }

        return $out;
    }

    /**
     * A spec-defined object (one with `properties`): keys must be strings.
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
     *
     * @throws UnexpectedResponseException
     */
    public static function map(array $data, string $key): array
    {
        return self::mapOrNull($data, $key) ?? throw self::missing($key);
    }

    /**
     * A spec-defined object (one with `properties`): keys must be strings.
     *
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>|null
     *
     * @throws UnexpectedResponseException
     */
    public static function mapOrNull(array $data, string $key): ?array
    {
        $value = $data[$key] ?? null;
        if ($value === null) {
            return null;
        }
        if (!is_array($value) || ($value !== [] && array_is_list($value))) {
            throw self::invalid($key, 'object');
        }

        return self::asMap($value, $key);
    }

    /**
     * A spec free-form object (`type: object` without `properties`). JSON object keys that look
     * numeric become integer keys in PHP, so keys are `array-key`.
     *
     * @param array<string, mixed> $data
     *
     * @return array<array-key, mixed>|null
     *
     * @throws UnexpectedResponseException
     */
    public static function freeFormOrNull(array $data, string $key): ?array
    {
        $value = $data[$key] ?? null;
        if ($value === null || is_array($value)) {
            return $value;
        }

        throw self::invalid($key, 'object');
    }

    /**
     * A list of spec-defined objects.
     *
     * @param array<string, mixed> $data
     *
     * @return list<array<string, mixed>>
     *
     * @throws UnexpectedResponseException
     */
    public static function listOfMaps(array $data, string $key): array
    {
        return self::listOfMapsOrNull($data, $key) ?? throw self::missing($key);
    }

    /**
     * A list of spec-defined objects.
     *
     * @param array<string, mixed> $data
     *
     * @return list<array<string, mixed>>|null
     *
     * @throws UnexpectedResponseException
     */
    public static function listOfMapsOrNull(array $data, string $key): ?array
    {
        $items = self::listOrNull($data, $key);
        if ($items === null) {
            return null;
        }

        $out = [];
        foreach ($items as $index => $item) {
            if (!is_array($item)) {
                throw self::invalid($key . '[' . $index . ']', 'object');
            }
            $out[] = self::asMap($item, $key . '[' . $index . ']');
        }

        return $out;
    }

    /**
     * @template E of BackedEnum
     *
     * @param array<string, mixed> $data
     * @param class-string<E>      $enum
     *
     * @return E|null
     *
     * @throws UnexpectedResponseException
     */
    public static function enumOrNull(array $data, string $key, string $enum): ?BackedEnum
    {
        $value = $data[$key] ?? null;
        if ($value === null) {
            return null;
        }
        if (!is_int($value) && !is_string($value)) {
            throw self::invalid($key, 'enum value');
        }

        return self::toEnum($value, $key, $enum);
    }

    /**
     * @template E of BackedEnum
     *
     * @param array<string, mixed> $data
     * @param class-string<E>      $enum
     *
     * @return list<E>|null
     *
     * @throws UnexpectedResponseException
     */
    public static function enumListOrNull(array $data, string $key, string $enum): ?array
    {
        $items = self::listOrNull($data, $key);
        if ($items === null) {
            return null;
        }

        $out = [];
        foreach ($items as $index => $item) {
            if (!is_int($item) && !is_string($item)) {
                throw self::invalid($key . '[' . $index . ']', 'enum value');
            }
            $out[] = self::toEnum($item, $key . '[' . $index . ']', $enum);
        }

        return $out;
    }

    /**
     * Ensures a decoded JSON object has only string keys.
     *
     * @param array<mixed> $value
     *
     * @return array<string, mixed>
     *
     * @throws UnexpectedResponseException
     */
    public static function asMap(array $value, string $context): array
    {
        $out = [];
        foreach ($value as $key => $item) {
            if (!is_string($key)) {
                throw self::invalid($context, 'object with string keys');
            }
            $out[$key] = $item;
        }

        return $out;
    }

    /**
     * Removes null values from a JSON-ready array (used by `toArray()` implementations).
     *
     * @param array<string, mixed> $values
     *
     * @return array<string, mixed>
     */
    public static function withoutNulls(array $values): array
    {
        $out = [];
        foreach ($values as $key => $value) {
            if ($value !== null) {
                $out[$key] = $value;
            }
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return list<mixed>|null
     *
     * @throws UnexpectedResponseException
     */
    private static function listOrNull(array $data, string $key): ?array
    {
        $value = $data[$key] ?? null;
        if ($value === null) {
            return null;
        }
        if (!is_array($value) || !array_is_list($value)) {
            throw self::invalid($key, 'array');
        }

        return $value;
    }

    /**
     * @template E of BackedEnum
     *
     * @param class-string<E> $enum
     *
     * @return E
     *
     * @throws UnexpectedResponseException
     */
    private static function toEnum(int|string $value, string $key, string $enum): BackedEnum
    {
        return $enum::tryFrom($value) ?? throw new UnexpectedResponseException(
            sprintf('Unexpected value for "%s": not a known %s case.', $key, $enum),
        );
    }

    private static function missing(string $key): UnexpectedResponseException
    {
        return new UnexpectedResponseException(sprintf('Required field "%s" is missing or null.', $key));
    }

    private static function invalid(string $key, string $expected): UnexpectedResponseException
    {
        return new UnexpectedResponseException(sprintf('Field "%s" is not a valid %s.', $key, $expected));
    }
}
