<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\AccessControlEnforcement;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.rate_limiting.redis` object of the Access Control Enforcement plugin (doc `TrafficControl/access-control-enforcement.md`).
 */
#[PluginSchema('TrafficControl/access-control-enforcement.md', '#/properties/config/properties/rate_limiting/properties/redis')]
final readonly class AccessControlEnforcementRateLimitingRedis implements Model
{
    /** Properties the plugin doc marks `x-encrypted`; redacted in __debugInfo(). */
    private const array ENCRYPTED = ['password', 'sentinelPassword'];

    /**
     * @param AccessControlEnforcementRateLimitingRedisCloudAuthentication|null $cloudAuthentication    Cloud auth related configs for connecting to a Cloud Provider's Redis instance.
     * @param int|null                                                          $clusterMaxRedirections Maximum retry attempts for redirection. Default: `5`.
     * @param list<AccessControlEnforcementRateLimitingRedisClusterNodes>|null  $clusterNodes           Cluster addresses to use for Redis connections when the `redis` strategy is defined.
     * @param int|null                                                          $connectTimeout         An integer representing a timeout in milliseconds. Default: `2000`.
     * @param bool|null                                                         $connectionIsProxied    If the connection to Redis is proxied (e.g. Default: `false`.
     * @param int|null                                                          $database               Database to use for the Redis connection when using the `redis` strategy Default: `0`.
     * @param string|null                                                       $host                   A string representing a host name, such as example.com. Default: `127.0.0.1`.
     * @param int|null                                                          $keepaliveBacklog       Limits the total number of opened connections for a pool.
     * @param int|null                                                          $keepalivePoolSize      The size limit for every cosocket connection pool associated with every remote server, per worker process. Default: `256`.
     * @param string|null                                                       $password               Password to use for Redis connections.
     * @param int|null                                                          $port                   An integer representing a port number between 0 and 65535, inclusive. Default: `6379`.
     * @param int|null                                                          $readTimeout            An integer representing a timeout in milliseconds. Default: `2000`.
     * @param int|null                                                          $sendTimeout            An integer representing a timeout in milliseconds. Default: `2000`.
     * @param string|null                                                       $sentinelMaster         Sentinel master to use for Redis connections.
     * @param list<AccessControlEnforcementRateLimitingRedisSentinelNodes>|null $sentinelNodes          Sentinel node addresses to use for Redis connections when the `redis` strategy is defined.
     * @param string|null                                                       $sentinelPassword       Sentinel password to authenticate with a Redis Sentinel instance.
     * @param AccessControlEnforcementRateLimitingRedisSentinelRole|null        $sentinelRole           Sentinel role to use for Redis connections when the `redis` strategy is defined.
     * @param string|null                                                       $sentinelUsername       Sentinel username to authenticate with a Redis Sentinel instance.
     * @param string|null                                                       $serverName             A string representing an SNI (server name indication) value for TLS.
     * @param bool|null                                                         $ssl                    If set to true, uses SSL to connect to Redis. Default: `false`.
     * @param bool|null                                                         $sslVerify              If set to true, verifies the validity of the server SSL certificate. Default: `true`.
     * @param string|null                                                       $username               Username to use for Redis connections.
     */
    public function __construct(
        public ?AccessControlEnforcementRateLimitingRedisCloudAuthentication $cloudAuthentication = null,
        public ?int $clusterMaxRedirections = null,
        public ?array $clusterNodes = null,
        public ?int $connectTimeout = null,
        public ?bool $connectionIsProxied = null,
        public ?int $database = null,
        public ?string $host = null,
        public ?int $keepaliveBacklog = null,
        public ?int $keepalivePoolSize = null,
        public ?string $password = null,
        public ?int $port = null,
        public ?int $readTimeout = null,
        public ?int $sendTimeout = null,
        public ?string $sentinelMaster = null,
        public ?array $sentinelNodes = null,
        public ?string $sentinelPassword = null,
        public ?AccessControlEnforcementRateLimitingRedisSentinelRole $sentinelRole = null,
        public ?string $sentinelUsername = null,
        public ?string $serverName = null,
        public ?bool $ssl = null,
        public ?bool $sslVerify = null,
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
        $clusterNodes = Data::listOfMapsOrNull($data, 'cluster_nodes');
        $sentinelNodes = Data::listOfMapsOrNull($data, 'sentinel_nodes');

        return new self(
            cloudAuthentication: $cloudAuthentication === null ? null : AccessControlEnforcementRateLimitingRedisCloudAuthentication::fromArray($cloudAuthentication),
            clusterMaxRedirections: Data::intOrNull($data, 'cluster_max_redirections'),
            clusterNodes: $clusterNodes === null ? null : array_map(AccessControlEnforcementRateLimitingRedisClusterNodes::fromArray(...), $clusterNodes),
            connectTimeout: Data::intOrNull($data, 'connect_timeout'),
            connectionIsProxied: Data::boolOrNull($data, 'connection_is_proxied'),
            database: Data::intOrNull($data, 'database'),
            host: Data::stringOrNull($data, 'host'),
            keepaliveBacklog: Data::intOrNull($data, 'keepalive_backlog'),
            keepalivePoolSize: Data::intOrNull($data, 'keepalive_pool_size'),
            password: Data::stringOrNull($data, 'password'),
            port: Data::intOrNull($data, 'port'),
            readTimeout: Data::intOrNull($data, 'read_timeout'),
            sendTimeout: Data::intOrNull($data, 'send_timeout'),
            sentinelMaster: Data::stringOrNull($data, 'sentinel_master'),
            sentinelNodes: $sentinelNodes === null ? null : array_map(AccessControlEnforcementRateLimitingRedisSentinelNodes::fromArray(...), $sentinelNodes),
            sentinelPassword: Data::stringOrNull($data, 'sentinel_password'),
            sentinelRole: Data::enumOrNull($data, 'sentinel_role', AccessControlEnforcementRateLimitingRedisSentinelRole::class),
            sentinelUsername: Data::stringOrNull($data, 'sentinel_username'),
            serverName: Data::stringOrNull($data, 'server_name'),
            ssl: Data::boolOrNull($data, 'ssl'),
            sslVerify: Data::boolOrNull($data, 'ssl_verify'),
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
            'cluster_max_redirections' => $this->clusterMaxRedirections,
            'cluster_nodes' => Data::toArrays($this->clusterNodes),
            'connect_timeout' => $this->connectTimeout,
            'connection_is_proxied' => $this->connectionIsProxied,
            'database' => $this->database,
            'host' => $this->host,
            'keepalive_backlog' => $this->keepaliveBacklog,
            'keepalive_pool_size' => $this->keepalivePoolSize,
            'password' => $this->password,
            'port' => $this->port,
            'read_timeout' => $this->readTimeout,
            'send_timeout' => $this->sendTimeout,
            'sentinel_master' => $this->sentinelMaster,
            'sentinel_nodes' => Data::toArrays($this->sentinelNodes),
            'sentinel_password' => $this->sentinelPassword,
            'sentinel_role' => $this->sentinelRole?->value,
            'sentinel_username' => $this->sentinelUsername,
            'server_name' => $this->serverName,
            'ssl' => $this->ssl,
            'ssl_verify' => $this->sslVerify,
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
