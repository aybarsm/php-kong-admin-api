<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\ResponseRateLimiting;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.redis` object of the Response Rate Limiting plugin (doc `TrafficControl/response-rate-limiting.md`).
 */
#[PluginSchema('TrafficControl/response-rate-limiting.md', '#/properties/config/properties/redis')]
final readonly class ResponseRateLimitingRedis implements Model
{
    /** Properties the plugin doc marks `x-encrypted`; redacted in __debugInfo(). */
    private const array ENCRYPTED = ['password'];

    /**
     * @param ResponseRateLimitingRedisCloudAuthentication|null $cloudAuthentication Cloud auth related configs for connecting to a Cloud Provider's Redis instance.
     * @param int|null                                          $database            Database to use for the Redis connection when using the `redis` strategy Default: `0`.
     * @param string|null                                       $host                A string representing a host name, such as example.com.
     * @param string|null                                       $password            Password to use for Redis connections.
     * @param int|null                                          $port                An integer representing a port number between 0 and 65535, inclusive. Default: `6379`.
     * @param string|null                                       $serverName          A string representing an SNI (server name indication) value for TLS.
     * @param bool|null                                         $ssl                 If set to true, uses SSL to connect to Redis. Default: `false`.
     * @param bool|null                                         $sslVerify           If set to true, verifies the validity of the server SSL certificate. Default: `true`.
     * @param int|null                                          $timeout             An integer representing a timeout in milliseconds. Default: `2000`.
     * @param string|null                                       $username            Username to use for Redis connections.
     */
    public function __construct(
        public ?ResponseRateLimitingRedisCloudAuthentication $cloudAuthentication = null,
        public ?int $database = null,
        public ?string $host = null,
        public ?string $password = null,
        public ?int $port = null,
        public ?string $serverName = null,
        public ?bool $ssl = null,
        public ?bool $sslVerify = null,
        public ?int $timeout = null,
        public ?string $username = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $cloudAuthentication = Data::mapOrNull($data, 'cloud_authentication');

        return new self(
            cloudAuthentication: $cloudAuthentication === null ? null : ResponseRateLimitingRedisCloudAuthentication::fromArray($cloudAuthentication),
            database: Data::intOrNull($data, 'database'),
            host: Data::stringOrNull($data, 'host'),
            password: Data::stringOrNull($data, 'password'),
            port: Data::intOrNull($data, 'port'),
            serverName: Data::stringOrNull($data, 'server_name'),
            ssl: Data::boolOrNull($data, 'ssl'),
            sslVerify: Data::boolOrNull($data, 'ssl_verify'),
            timeout: Data::intOrNull($data, 'timeout'),
            username: Data::stringOrNull($data, 'username'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'cloud_authentication' => $this->cloudAuthentication?->toArray(),
            'database' => $this->database,
            'host' => $this->host,
            'password' => $this->password,
            'port' => $this->port,
            'server_name' => $this->serverName,
            'ssl' => $this->ssl,
            'ssl_verify' => $this->sslVerify,
            'timeout' => $this->timeout,
            'username' => $this->username,
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
