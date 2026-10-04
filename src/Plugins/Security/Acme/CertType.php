<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Security\Acme;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.cert_type` in the ACME Plugin doc.
 *
 * The certificate type to create.
 */
#[PluginSchema('Security/acme.md', '#/properties/config/properties/cert_type')]
enum CertType: string
{
    case Ecc = 'ecc';
    case Rsa = 'rsa';
}
