<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Security\Acme;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.storage_config.vault` object of the ACME plugin (doc `Security/acme.md`).
 */
#[PluginSchema('Security/acme.md', '#/properties/config/properties/storage_config/properties/vault')]
final readonly class AcmeStorageConfigVault implements Model
{
    /** Properties the plugin doc marks `x-encrypted`; redacted in __debugInfo(). */
    private const array ENCRYPTED = ['token'];

    /**
     * @param AcmeStorageConfigVaultAuthMethod|null $authMethod    Auth Method, default to token, can be 'token' or 'kubernetes'. Default: `token`.
     * @param string|null                           $authPath      Vault's authentication path to use.
     * @param string|null                           $authRole      The role to try and assign.
     * @param string|null                           $host          A string representing a host name, such as example.com.
     * @param bool|null                             $https         Boolean representation of https. Default: `false`.
     * @param string|null                           $jwtPath       The path to the JWT.
     * @param string|null                           $kvPath        KV prefix path.
     * @param int|null                              $port          An integer representing a port number between 0 and 65535, inclusive.
     * @param float|null                            $timeout       Timeout in milliseconds.
     * @param string|null                           $tlsServerName SNI used in request, default to host if omitted.
     * @param bool|null                             $tlsVerify     Turn on TLS verification. Default: `true`.
     * @param string|null                           $token         Consul ACL token.
     */
    public function __construct(
        public ?AcmeStorageConfigVaultAuthMethod $authMethod = null,
        public ?string $authPath = null,
        public ?string $authRole = null,
        public ?string $host = null,
        public ?bool $https = null,
        public ?string $jwtPath = null,
        public ?string $kvPath = null,
        public ?int $port = null,
        public ?float $timeout = null,
        public ?string $tlsServerName = null,
        public ?bool $tlsVerify = null,
        public ?string $token = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            authMethod: Data::enumOrNull($data, 'auth_method', AcmeStorageConfigVaultAuthMethod::class),
            authPath: Data::stringOrNull($data, 'auth_path'),
            authRole: Data::stringOrNull($data, 'auth_role'),
            host: Data::stringOrNull($data, 'host'),
            https: Data::boolOrNull($data, 'https'),
            jwtPath: Data::stringOrNull($data, 'jwt_path'),
            kvPath: Data::stringOrNull($data, 'kv_path'),
            port: Data::intOrNull($data, 'port'),
            timeout: Data::floatOrNull($data, 'timeout'),
            tlsServerName: Data::stringOrNull($data, 'tls_server_name'),
            tlsVerify: Data::boolOrNull($data, 'tls_verify'),
            token: Data::stringOrNull($data, 'token'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'auth_method' => $this->authMethod?->value,
            'auth_path' => $this->authPath,
            'auth_role' => $this->authRole,
            'host' => $this->host,
            'https' => $this->https,
            'jwt_path' => $this->jwtPath,
            'kv_path' => $this->kvPath,
            'port' => $this->port,
            'timeout' => $this->timeout,
            'tls_server_name' => $this->tlsServerName,
            'tls_verify' => $this->tlsVerify,
            'token' => $this->token,
        ]);
    }

    /**
     * Redacts `x-encrypted` values.
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        $values = get_object_vars($this);
        foreach (self::ENCRYPTED as $property) {
            if ($values[$property] !== null) {
                $values[$property] = '***';
            }
        }

        return $values;
    }
}
