<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Security\Acme;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.storage_config.consul` object of the ACME plugin (doc `Security/acme.md`).
 */
#[PluginSchema('Security/acme.md', '#/properties/config/properties/storage_config/properties/consul')]
final readonly class AcmeStorageConfigConsul implements Model
{
    /** Properties the plugin doc marks `x-encrypted`; redacted in __debugInfo(). */
    private const array ENCRYPTED = ['token'];

    /**
     * @param string|null $host    A string representing a host name, such as example.com.
     * @param bool|null   $https   Boolean representation of https. Default: `false`.
     * @param string|null $kvPath  KV prefix path.
     * @param int|null    $port    An integer representing a port number between 0 and 65535, inclusive.
     * @param float|null  $timeout Timeout in milliseconds.
     * @param string|null $token   Consul ACL token.
     */
    public function __construct(
        public ?string $host = null,
        public ?bool $https = null,
        public ?string $kvPath = null,
        public ?int $port = null,
        public ?float $timeout = null,
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
            host: Data::stringOrNull($data, 'host'),
            https: Data::boolOrNull($data, 'https'),
            kvPath: Data::stringOrNull($data, 'kv_path'),
            port: Data::intOrNull($data, 'port'),
            timeout: Data::floatOrNull($data, 'timeout'),
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
            'host' => $this->host,
            'https' => $this->https,
            'kv_path' => $this->kvPath,
            'port' => $this->port,
            'timeout' => $this->timeout,
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
