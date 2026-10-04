<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Security\Acme;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.storage` in the ACME Plugin doc.
 *
 * The backend storage type to use.
 */
#[PluginSchema('Security/acme.md', '#/properties/config/properties/storage')]
enum Storage: string
{
    case Consul = 'consul';
    case Kong = 'kong';
    case Redis = 'redis';
    case Shm = 'shm';
    case Vault = 'vault';
}
