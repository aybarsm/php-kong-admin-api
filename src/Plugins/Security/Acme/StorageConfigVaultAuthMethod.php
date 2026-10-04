<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Security\Acme;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;

/**
 * Values of `config.storage_config.vault.auth_method` in the ACME Plugin doc.
 *
 * Auth Method, default to token, can be 'token' or 'kubernetes'.
 */
#[PluginSchema('Security/acme.md', '#/properties/config/properties/storage_config/properties/vault/properties/auth_method')]
enum StorageConfigVaultAuthMethod: string
{
    case Kubernetes = 'kubernetes';
    case Token = 'token';
}
