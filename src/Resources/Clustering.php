<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Exceptions\ValidationException;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\DataPlane;
use Aybarsm\Kong\AdminApi\Models\DataPlaneStatus;

/**
 * Hybrid-mode clustering (spec tag "Clustering"): `/clustering/data-planes` and `/clustering/status`.
 * Both are available only on a control plane; otherwise Kong answers 400. All paths are global.
 */
final readonly class Clustering extends AbstractResource
{
    /**
     * The data planes connected to this control plane (operationId `get-data-planes`; spec response `{data}`).
     *
     * @return list<DataPlane>
     *
     * @throws ValidationException with status 400 when Kong is not running as a control plane
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/clustering/data-planes', 'get-data-planes', OperationScope::GlobalOnly)]
    public function dataPlanes(): array
    {
        $payload = $this->object(Transport::METHOD_GET, $this->path(OperationScope::GlobalOnly, 'clustering', 'data-planes'));

        return array_map(DataPlane::fromArray(...), Data::listOfMaps($payload, 'data'));
    }

    /**
     * Data-plane status keyed by node ID (operationId `get-dataplane-status`). The spec marks this response
     * with a `Deprecation` header; prefer dataPlanes().
     *
     * @return array<array-key, DataPlaneStatus>
     *
     * @throws ValidationException with status 400 when Kong is not running as a control plane
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/clustering/status', 'get-dataplane-status', OperationScope::GlobalOnly)]
    public function status(): array
    {
        $path = $this->path(OperationScope::GlobalOnly, 'clustering', 'status');
        $nodes = Data::mapOfMapsOrNull(['status' => $this->transport->json(Transport::METHOD_GET, $path)], 'status') ?? [];

        return array_map(DataPlaneStatus::fromArray(...), $nodes);
    }
}
