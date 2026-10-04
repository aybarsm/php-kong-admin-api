<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\InvalidArgumentException;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Exceptions\ServerException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\DnsStatus;
use Aybarsm\Kong\AdminApi\Models\FipsStatus;
use Aybarsm\Kong\AdminApi\Models\KongInfo;
use Aybarsm\Kong\AdminApi\Models\NodeStatus;
use Aybarsm\Kong\AdminApi\Models\Timers;

/**
 * Node information (spec tag "Information"): `/`, `/status`, `/status/dns`, `/endpoints`, `/timers`,
 * `/fips-status`, and `HEAD`/`OPTIONS /{endpoint}`. All paths are global.
 */
final readonly class Information extends AbstractResource
{
    /**
     * Node and configuration details (operationId `geInfo`).
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/', 'geInfo', OperationScope::GlobalOnly)]
    public function info(): KongInfo
    {
        return KongInfo::fromArray($this->object(Transport::METHOD_GET, $this->path(OperationScope::GlobalOnly)));
    }

    /**
     * Node status (operationId `get-status`).
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/status', 'get-status', OperationScope::GlobalOnly)]
    public function status(): NodeStatus
    {
        return NodeStatus::fromArray($this->object(Transport::METHOD_GET, $this->path(OperationScope::GlobalOnly, 'status')));
    }

    /**
     * DNS client status (operationId `get-dns-status`).
     *
     * @throws ServerException with status 501 when the legacy DNS client is in use
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/status/dns', 'get-dns-status', OperationScope::GlobalOnly)]
    public function dnsStatus(): DnsStatus
    {
        return DnsStatus::fromArray($this->object(Transport::METHOD_GET, $this->path(OperationScope::GlobalOnly, 'status', 'dns')));
    }

    /**
     * Every Admin API endpoint path the node serves (operationId `get-endpoints`; spec response `{data}`).
     *
     * @return list<string>
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/endpoints', 'get-endpoints', OperationScope::GlobalOnly)]
    public function endpoints(): array
    {
        return Data::stringList($this->object(Transport::METHOD_GET, $this->path(OperationScope::GlobalOnly, 'endpoints')), 'data');
    }

    /**
     * Timer debug information (operationId `get-timers`).
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/timers', 'get-timers', OperationScope::GlobalOnly)]
    public function timers(): Timers
    {
        return Timers::fromArray($this->object(Transport::METHOD_GET, $this->path(OperationScope::GlobalOnly, 'timers')));
    }

    /**
     * FIPS mode status (operationId `list-fips-status`).
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/fips-status', 'list-fips-status', OperationScope::GlobalOnly)]
    public function fipsStatus(): FipsStatus
    {
        return FipsStatus::fromArray($this->object(Transport::METHOD_GET, $this->path(OperationScope::GlobalOnly, 'fips-status')));
    }

    /**
     * Whether an endpoint exists (operationId `list-endpoints`, HEAD): true for 204, false for 404.
     *
     * @param string $endpoint one path segment, e.g. `services`
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $endpoint is empty
     */
    #[Operation(Transport::METHOD_HEAD, '/{endpoint}', 'list-endpoints', OperationScope::GlobalOnly)]
    public function endpointExists(string $endpoint): bool
    {
        return $this->transport->exists(Transport::METHOD_HEAD, $this->path(OperationScope::GlobalOnly, $endpoint));
    }

    /**
     * The HTTP methods an endpoint supports (operationId `list-options-endpoint`, OPTIONS), read from the
     * `Allow` response header the spec defines (`ListEndpointSupportedMethodsResponse`).
     *
     * @param string $endpoint one path segment, e.g. `services`
     *
     * @return list<string>
     *
     * @throws KongApiException
     * @throws InvalidArgumentException when $endpoint is empty
     */
    #[Operation(Transport::METHOD_OPTIONS, '/{endpoint}', 'list-options-endpoint', OperationScope::GlobalOnly)]
    public function allowedMethods(string $endpoint): array
    {
        return $this->transport->headerValues(Transport::METHOD_OPTIONS, $this->path(OperationScope::GlobalOnly, $endpoint), 'Allow');
    }
}
