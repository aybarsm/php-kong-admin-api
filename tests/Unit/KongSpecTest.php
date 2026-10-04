<?php

declare(strict_types=1);

use Aybarsm\Kong\AdminApi\KongSpec;
use Aybarsm\Kong\AdminApi\Tests\Support\Spec;

covers(KongSpec::class);

it('records the Kong version of the canonical spec', function (): void {
    expect(is_file(Spec::path()))->toBeTrue()
        ->and(KongSpec::VERSION)->toBe(Spec::infoVersion());
});

it('names the canonical spec file after the major.minor version', function (): void {
    [$major, $minor] = explode('.', KongSpec::VERSION);

    expect(KongSpec::SPEC_FILE)->toBe(sprintf('v%s.%s.json', $major, $minor));
});
