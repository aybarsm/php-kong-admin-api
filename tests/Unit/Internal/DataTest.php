<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Enums\HttpsRedirectStatusCode;
use Aybarsm\Kong\AdminApi\Enums\Protocol;
use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Internal\Data;

covers(Data::class);

it('reads present values of every scalar type', function (): void {
    $data = ['s' => 'x', 'i' => 7, 'f' => 1.5, 'fi' => 2, 'b' => false];

    expect(Data::string($data, 's'))->toBe('x')
        ->and(Data::int($data, 'i'))->toBe(7)
        ->and(Data::floatOrNull($data, 'f'))->toBe(1.5)
        ->and(Data::floatOrNull($data, 'fi'))->toBe(2.0)
        ->and(Data::bool($data, 'b'))->toBeFalse();
});

it('reads absent and null keys as null', function (string $key): void {
    $data = ['null' => null];

    expect(Data::stringOrNull($data, $key))->toBeNull()
        ->and(Data::intOrNull($data, $key))->toBeNull()
        ->and(Data::floatOrNull($data, $key))->toBeNull()
        ->and(Data::boolOrNull($data, $key))->toBeNull()
        ->and(Data::stringListOrNull($data, $key))->toBeNull()
        ->and(Data::intListOrNull($data, $key))->toBeNull()
        ->and(Data::mapOrNull($data, $key))->toBeNull()
        ->and(Data::freeFormOrNull($data, $key))->toBeNull()
        ->and(Data::listOfMapsOrNull($data, $key))->toBeNull()
        ->and(Data::enumOrNull($data, $key, Protocol::class))->toBeNull()
        ->and(Data::enumListOrNull($data, $key, Protocol::class))->toBeNull();
})->with(['absent', 'null']);

it('throws on missing required values', function (callable $read, string $key): void {
    expect($read)->toThrow(UnexpectedResponseException::class, sprintf('Required field "%s" is missing or null.', $key));
})->with([
    'string' => [fn (): string => Data::string([], 'a'), 'a'],
    'int' => [fn (): int => Data::int(['b' => null], 'b'), 'b'],
    'bool' => [fn (): bool => Data::bool([], 'c'), 'c'],
    'map' => [fn (): array => Data::map([], 'd'), 'd'],
    'free-form' => [fn (): array => Data::freeForm(['f' => null], 'f'), 'f'],
    'string list' => [fn (): array => Data::stringList([], 'l'), 'l'],
    'list of maps' => [fn (): array => Data::listOfMaps([], 'e'), 'e'],
]);

it('throws on values of the wrong type', function (callable $read, string $message): void {
    expect($read)->toThrow(UnexpectedResponseException::class, $message);
})->with([
    'string' => [fn (): ?string => Data::stringOrNull(['k' => 1], 'k'), 'Field "k" is not a valid string.'],
    'int from float' => [fn (): ?int => Data::intOrNull(['k' => 1.5], 'k'), 'Field "k" is not a valid integer.'],
    'int from string' => [fn (): ?int => Data::intOrNull(['k' => '1'], 'k'), 'Field "k" is not a valid integer.'],
    'number' => [fn (): ?float => Data::floatOrNull(['k' => '1.5'], 'k'), 'Field "k" is not a valid number.'],
    'bool' => [fn (): ?bool => Data::boolOrNull(['k' => 1], 'k'), 'Field "k" is not a valid boolean.'],
    'string list items' => [fn (): ?array => Data::stringListOrNull(['k' => ['a', 1]], 'k'), 'Field "k" is not a valid list of strings.'],
    'int list items' => [fn (): ?array => Data::intListOrNull(['k' => [1, 'a']], 'k'), 'Field "k" is not a valid list of integers.'],
    'list is object' => [fn (): ?array => Data::stringListOrNull(['k' => ['a' => 'b']], 'k'), 'Field "k" is not a valid array.'],
    'list is scalar' => [fn (): ?array => Data::stringListOrNull(['k' => 'a'], 'k'), 'Field "k" is not a valid array.'],
    'map is list' => [fn (): ?array => Data::mapOrNull(['k' => [1, 2]], 'k'), 'Field "k" is not a valid object.'],
    'map is scalar' => [fn (): ?array => Data::mapOrNull(['k' => 'x'], 'k'), 'Field "k" is not a valid object.'],
    'map has int keys' => [fn (): ?array => Data::mapOrNull(['k' => [5 => 'x']], 'k'), 'Field "k" is not a valid object with string keys.'],
    'free-form is scalar' => [fn (): ?array => Data::freeFormOrNull(['k' => true], 'k'), 'Field "k" is not a valid object.'],
    'header map is list' => [fn (): ?array => Data::stringListMapOrNull(['k' => [['a']]], 'k'), 'Field "k" is not a valid object.'],
    'header map is scalar' => [fn (): ?array => Data::stringListMapOrNull(['k' => 'a'], 'k'), 'Field "k" is not a valid object.'],
    'header values not a list' => [fn (): ?array => Data::stringListMapOrNull(['k' => ['h' => 'a']], 'k'), 'Field "k.h" is not a valid list of strings.'],
    'header values object' => [fn (): ?array => Data::stringListMapOrNull(['k' => ['h' => ['x' => 'a']]], 'k'), 'Field "k.h" is not a valid list of strings.'],
    'header value not string' => [fn (): ?array => Data::stringListMapOrNull(['k' => ['h' => [1]]], 'k'), 'Field "k.h" is not a valid list of strings.'],
    'string map is list' => [fn (): ?array => Data::stringMapOrNull(['k' => ['a']], 'k'), 'Field "k" is not a valid object.'],
    'string map is scalar' => [fn (): ?array => Data::stringMapOrNull(['k' => 'a'], 'k'), 'Field "k" is not a valid object.'],
    'string map value' => [fn (): ?array => Data::stringMapOrNull(['k' => ['a' => 1]], 'k'), 'Field "k.a" is not a valid string.'],
    'map of maps is list' => [fn (): ?array => Data::mapOfMapsOrNull(['k' => [['a' => 1]]], 'k'), 'Field "k" is not a valid object.'],
    'map of maps is scalar' => [fn (): ?array => Data::mapOfMapsOrNull(['k' => 'a'], 'k'), 'Field "k" is not a valid object.'],
    'map of maps value' => [fn (): ?array => Data::mapOfMapsOrNull(['k' => ['n' => 1]], 'k'), 'Field "k.n" is not a valid object.'],
    'list of maps item' => [fn (): ?array => Data::listOfMapsOrNull(['k' => [['a' => 1], 'x']], 'k'), 'Field "k[1]" is not a valid object.'],
    'enum type' => [fn (): ?Protocol => Data::enumOrNull(['k' => 1.5], 'k', Protocol::class), 'Field "k" is not a valid enum value.'],
    'enum case' => [fn (): ?Protocol => Data::enumOrNull(['k' => 'gopher'], 'k', Protocol::class), 'Unexpected value for "k"'],
    'enum list type' => [fn (): ?array => Data::enumListOrNull(['k' => ['http', true]], 'k', Protocol::class), 'Field "k[1]" is not a valid enum value.'],
    'enum list case' => [fn (): ?array => Data::enumListOrNull(['k' => ['http', 'nope']], 'k', Protocol::class), 'Unexpected value for "k[1]"'],
]);

it('reads lists, maps, free-form objects and enums', function (): void {
    $data = [
        'strings' => ['a', 'b'],
        'ints' => [200, 302],
        'map' => ['x' => 1],
        'empty' => [],
        'free' => ['200' => 'ok', 'name' => 'x'],
        'rows' => [['a' => 1], ['b' => 2]],
        'protocol' => 'https',
        'protocols' => ['grpc', 'tls_passthrough'],
        'code' => 426,
    ];

    expect(Data::stringListOrNull($data, 'strings'))->toBe(['a', 'b'])
        ->and(Data::stringList($data, 'strings'))->toBe(['a', 'b'])
        ->and(Data::intListOrNull($data, 'ints'))->toBe([200, 302])
        ->and(Data::map($data, 'map'))->toBe(['x' => 1])
        ->and(Data::freeForm($data, 'free'))->toBe([200 => 'ok', 'name' => 'x'])
        ->and(Data::mapOrNull($data, 'empty'))->toBe([])
        ->and(Data::freeFormOrNull($data, 'free'))->toBe([200 => 'ok', 'name' => 'x'])
        ->and(Data::listOfMaps($data, 'rows'))->toBe([['a' => 1], ['b' => 2]])
        ->and(Data::enumOrNull($data, 'protocol', Protocol::class))->toBe(Protocol::Https)
        ->and(Data::enumListOrNull($data, 'protocols', Protocol::class))->toBe([Protocol::Grpc, Protocol::TlsPassthrough])
        ->and(Data::enumOrNull($data, 'code', HttpsRedirectStatusCode::class))->toBe(HttpsRedirectStatusCode::UpgradeRequired);
});

it('checks for string keys and drops only nulls', function (): void {
    expect(Data::asMap(['a' => 1], 'ctx'))->toBe(['a' => 1])
        ->and(fn (): array => Data::asMap([1, 2], 'ctx'))->toThrow(UnexpectedResponseException::class, 'Field "ctx"')
        ->and(Data::withoutNulls(['a' => null, 'b' => 0, 'c' => false, 'd' => '', 'e' => []]))
        ->toBe(['b' => 0, 'c' => false, 'd' => '', 'e' => []]);
});

it('reads header-style maps of string lists', function (): void {
    expect(Data::stringListMapOrNull(['h' => ['x-a' => ['1', '2'], 'x-b' => []]], 'h'))->toBe(['x-a' => ['1', '2'], 'x-b' => []])
        ->and(Data::stringListMapOrNull(['h' => []], 'h'))->toBe([])
        ->and(Data::stringListMapOrNull([], 'h'))->toBeNull();
});

it('converts enum and model lists for toArray()', function (): void {
    expect(Data::enumValues([Protocol::Http, Protocol::Wss]))->toBe(['http', 'wss'])
        ->and(Data::enumValues(null))->toBeNull()
        ->and(Data::toArrays([new Aybarsm\Kong\AdminApi\Models\Shared\IpPort('1.2.3.4', 1)]))->toBe([['ip' => '1.2.3.4', 'port' => 1]])
        ->and(Data::toArrays(null))->toBeNull();
});

it('reads string maps', function (): void {
    expect(Data::stringMapOrNull(['m' => ['a' => 'b']], 'm'))->toBe(['a' => 'b'])
        ->and(Data::stringMapOrNull(['m' => []], 'm'))->toBe([])
        ->and(Data::stringMapOrNull([], 'm'))->toBeNull();
});

it('reads maps of objects and serialises keyed DTO maps', function (): void {
    expect(Data::mapOfMapsOrNull(['m' => ['a' => ['x' => 1], 'b' => []]], 'm'))->toBe(['a' => ['x' => 1], 'b' => []])
        ->and(Data::mapOfMapsOrNull(['m' => []], 'm'))->toBe([])
        ->and(Data::mapOfMapsOrNull([], 'm'))->toBeNull()
        ->and(Data::toArrayMap(['n1' => new Aybarsm\Kong\AdminApi\Models\Shared\IpPort('1.2.3.4')]))->toBe(['n1' => ['ip' => '1.2.3.4']])
        ->and(Data::toArrayMap(null))->toBeNull();
});
