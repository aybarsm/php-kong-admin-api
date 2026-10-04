<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\UnexpectedResponseException;
use Aybarsm\Kong\AdminApi\Models\Plugin;
use Aybarsm\Kong\AdminApi\Plugins\PluginRegistry;
use Aybarsm\Kong\AdminApi\Plugins\TrafficControl\RequestSizeLimiting\RequestSizeLimitingConfig;
use Aybarsm\Kong\AdminApi\Plugins\TrafficControl\RequestSizeLimiting\SizeUnit;
use Aybarsm\Kong\AdminApi\Tests\Support\PluginDocs;

covers(PluginRegistry::class);

it('registers exactly the plugins with a non-blocked doc, by wire name', function (): void {
    $expected = [];
    foreach (array_diff(PluginDocs::all(), PluginDocs::blocked()) as $doc) {
        $expected[PluginDocs::name($doc)] = true;
    }
    ksort($expected);
    $registered = array_fill_keys(array_keys(PluginRegistry::configs()), true);
    ksort($registered);

    expect($registered)->toBe($expected)
        ->and(PluginRegistry::has('request-size-limiting'))->toBeTrue()
        ->and(PluginRegistry::has('rate-limiting-advanced'))->toBeFalse();
});

it('maps a documented plugin to its typed config and an undocumented one to null', function (): void {
    $config = PluginRegistry::config(new Plugin(name: 'request-size-limiting', config: ['size_unit' => 'kilobytes', 'allowed_payload_size' => 10]));

    expect($config)->toBeInstanceOf(RequestSizeLimitingConfig::class)
        ->and($config instanceof RequestSizeLimitingConfig ? $config->sizeUnit : null)->toBe(SizeUnit::Kilobytes)
        ->and(PluginRegistry::config(new Plugin(name: 'my-custom-plugin', config: ['anything' => true])))->toBeNull();
});

it('rejects a config that does not match the doc', function (): void {
    expect(fn () => PluginRegistry::config(new Plugin(name: 'request-size-limiting', config: ['size_unit' => 'gigabytes'])))
        ->toThrow(UnexpectedResponseException::class, 'Unexpected value for "size_unit"');
});
