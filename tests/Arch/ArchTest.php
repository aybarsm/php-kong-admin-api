<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\KongClient;
use Aybarsm\Kong\AdminApi\Resources\AbstractResource;
use Pest\Preset;

/*
 * Written as typed closures (not the `arch()->…` chain) so PHPStan level 9 can analyse them.
 */

it('passes the php and security presets', function (): void {
    (new Preset())->php();
    (new Preset())->security();
});

it('uses strict types everywhere', function (): void {
    expect('Aybarsm\Kong\AdminApi')->toUseStrictTypes();
});

it('makes DTOs, config, pagination and attributes final readonly classes', function (): void {
    expect(['Aybarsm\Kong\AdminApi\Models', 'Aybarsm\Kong\AdminApi\Config', 'Aybarsm\Kong\AdminApi\Pagination', 'Aybarsm\Kong\AdminApi\Attributes'])
        ->classes()->toBeFinal()
        ->and(['Aybarsm\Kong\AdminApi\Models', 'Aybarsm\Kong\AdminApi\Config', 'Aybarsm\Kong\AdminApi\Pagination', 'Aybarsm\Kong\AdminApi\Attributes'])
        ->classes()->toBeReadonly();
});

it('makes plugin classes final readonly (enum backing is checked against the docs in PluginConformanceTest)', function (): void {
    expect('Aybarsm\Kong\AdminApi\Plugins')->classes()->toBeFinal()
        ->and('Aybarsm\Kong\AdminApi\Plugins')->classes()->toBeReadonly();
});

it('makes resources final readonly subclasses of the shared base', function (): void {
    expect('Aybarsm\Kong\AdminApi\Resources')->classes()->toBeReadonly();
    expect('Aybarsm\Kong\AdminApi\Resources')->classes()->toBeFinal()->ignoring(AbstractResource::class);
    expect('Aybarsm\Kong\AdminApi\Resources')->classes()->toExtend(AbstractResource::class)->ignoring(AbstractResource::class);
});

it('makes exceptions final, except the base', function (): void {
    expect('Aybarsm\Kong\AdminApi\Exceptions')->classes()->toBeFinal()->ignoring(KongApiException::class);
});

it('keeps HTTP out of resources, models and pagination', function (): void {
    expect('Aybarsm\Kong\AdminApi\Resources')->not->toUse(['Psr\Http\Message', 'Psr\Http\Client', 'GuzzleHttp']);
    expect(['Aybarsm\Kong\AdminApi\Models', 'Aybarsm\Kong\AdminApi\Pagination', 'Aybarsm\Kong\AdminApi\Plugins'])
        ->not->toUse(['Psr\Http\Message', 'Psr\Http\Client', 'GuzzleHttp', Transport::class]);
});

it('uses Guzzle, PSR-7 and PSR-18 only in the client factory and the transport', function (): void {
    expect('GuzzleHttp')->toOnlyBeUsedIn([KongClient::class, Transport::class]);
    expect(['Psr\Http\Message', 'Psr\Http\Client'])->toOnlyBeUsedIn([KongClient::class, Transport::class]);
});

it('leaves no debugging calls behind', function (): void {
    expect(['var_dump', 'dd', 'dump', 'print_r', 'var_export', 'error_log', 'printf'])->not->toBeUsed();
});

it('backs every enum with the spec values', function (): void {
    expect('Aybarsm\Kong\AdminApi\Enums')->toBeStringBackedEnums()->ignoring('Aybarsm\Kong\AdminApi\Enums\HttpsRedirectStatusCode');
    expect('Aybarsm\Kong\AdminApi\Enums\HttpsRedirectStatusCode')->toBeIntBackedEnums();
});
