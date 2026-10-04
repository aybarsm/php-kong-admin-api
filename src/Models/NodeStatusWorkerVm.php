<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `memory.workers_lua_vms[]` object of NodeStatus (spec `#/components/responses/GetNodeStatusResponse/content/application~1json/schema.memory.workers_lua_vms[]`).
 */
#[Schema('#/components/responses/GetNodeStatusResponse/content/application~1json/schema/properties/memory/properties/workers_lua_vms')]
final readonly class NodeStatusWorkerVm implements Model
{
    /**
     * @param string|null $httpAllocatedGc Memory allocated to HTTP garbage collection.
     * @param int|null    $pid             Worker process ID.
     */
    public function __construct(
        public ?string $httpAllocatedGc = null,
        public ?int $pid = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            httpAllocatedGc: Data::stringOrNull($data, 'http_allocated_gc'),
            pid: Data::intOrNull($data, 'pid'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'http_allocated_gc' => $this->httpAllocatedGc,
            'pid' => $this->pid,
        ]);
    }
}
