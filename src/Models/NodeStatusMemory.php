<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `memory` object of NodeStatus (spec `#/components/responses/GetNodeStatusResponse/content/application~1json/schema.memory`).
 */
#[Schema('#/components/responses/GetNodeStatusResponse/content/application~1json/schema/properties/memory')]
final readonly class NodeStatusMemory implements Model
{
    /**
     * @param array<array-key, mixed>|null  $luaSharedDicts Memory details for shared Lua dictionaries.
     * @param list<NodeStatusWorkerVm>|null $workersLuaVms  Metrics for Lua VMs for each worker.
     */
    public function __construct(
        public ?array $luaSharedDicts = null,
        public ?array $workersLuaVms = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $workersLuaVms = Data::listOfMapsOrNull($data, 'workers_lua_vms');

        return new self(
            luaSharedDicts: Data::freeFormOrNull($data, 'lua_shared_dicts'),
            workersLuaVms: $workersLuaVms === null ? null : array_map(NodeStatusWorkerVm::fromArray(...), $workersLuaVms),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'lua_shared_dicts' => $this->luaSharedDicts,
            'workers_lua_vms' => Data::toArrays($this->workersLuaVms),
        ]);
    }
}
