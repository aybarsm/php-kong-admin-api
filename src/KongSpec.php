<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi;

/**
 * The Kong Admin API specification this package was built against.
 *
 * The spec file lives in `resources/kong-admin-api/` in the source repository and is the
 * only authority for every endpoint, parameter and schema implemented here.
 */
final class KongSpec
{
    /** Kong Gateway version, taken from the spec's `info.version`. */
    public const string VERSION = '3.16.0';

    /** Canonical spec file name under `resources/kong-admin-api/`. */
    public const string SPEC_FILE = 'v3.16.json';

    /** @codeCoverageIgnore Static holder; never instantiated. */
    private function __construct()
    {
    }
}
