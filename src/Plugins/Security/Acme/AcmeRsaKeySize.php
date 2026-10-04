<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Security\Acme;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.rsa_key_size` in the ACME Plugin doc.
 *
 * RSA private key size for the certificate.
 */
#[PluginSchema('Security/acme.md', '#/properties/config/properties/rsa_key_size')]
enum AcmeRsaKeySize: int
{
    case Value2048 = 2048;
    case Value3072 = 3072;
    case Value4096 = 4096;
}
