<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Resources;

use Aybarsm\Kong\AdminApi\Attributes\Operation;
use Aybarsm\Kong\AdminApi\Enums\LogLevel;
use Aybarsm\Kong\AdminApi\Enums\OperationScope;
use Aybarsm\Kong\AdminApi\Exceptions\KongApiException;
use Aybarsm\Kong\AdminApi\Internal\Transport;
use Aybarsm\Kong\AdminApi\Models\NodeLogLevel;

/**
 * Log levels (spec tag "Debug"): `/debug/node/log-level` and `/debug/cluster/…/log-level/{logLevel}`.
 * All paths are global.
 */
final readonly class Debug extends AbstractResource
{
    /**
     * The node's current log level (operationId `get-debug-node-log-level`).
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_GET, '/debug/node/log-level', 'get-debug-node-log-level', OperationScope::GlobalOnly)]
    public function nodeLogLevel(): NodeLogLevel
    {
        return NodeLogLevel::fromArray($this->object(Transport::METHOD_GET, $this->path(OperationScope::GlobalOnly, 'debug', 'node', 'log-level')));
    }

    /**
     * Set the node's log level (operationId `get-debug-node-log-level-log_level`, PUT).
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_PUT, '/debug/node/log-level/{logLevel}', 'get-debug-node-log-level-log_level', OperationScope::GlobalOnly)]
    public function setNodeLogLevel(LogLevel $level): NodeLogLevel
    {
        return NodeLogLevel::fromArray($this->object(
            Transport::METHOD_PUT,
            $this->path(OperationScope::GlobalOnly, 'debug', 'node', 'log-level', $level->value),
        ));
    }

    /**
     * Set the log level of every node in the cluster (operationId `update-debug-cluster-log-level`, PUT).
     * The spec defines no response body (spec-notes Q15).
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_PUT, '/debug/cluster/log-level/{logLevel}', 'update-debug-cluster-log-level', OperationScope::GlobalOnly)]
    public function setClusterLogLevel(LogLevel $level): void
    {
        $this->none(Transport::METHOD_PUT, $this->path(OperationScope::GlobalOnly, 'debug', 'cluster', 'log-level', $level->value));
    }

    /**
     * Set the log level of every control-plane node (operationId
     * `create-debug-cluster-control-planes-nodes-log-level`, PUT). The spec defines no response body (spec-notes Q15).
     *
     * @throws KongApiException
     */
    #[Operation(Transport::METHOD_PUT, '/debug/cluster/control-planes-nodes/log-level/{logLevel}', 'create-debug-cluster-control-planes-nodes-log-level', OperationScope::GlobalOnly)]
    public function setControlPlanesLogLevel(LogLevel $level): void
    {
        $this->none(Transport::METHOD_PUT, $this->path(OperationScope::GlobalOnly, 'debug', 'cluster', 'control-planes-nodes', 'log-level', $level->value));
    }
}
