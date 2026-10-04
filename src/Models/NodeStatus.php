<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Node status from `GET /status` (spec response `GetNodeStatusResponse`).
 */
#[Schema('#/components/responses/GetNodeStatusResponse/content/application~1json/schema')]
final readonly class NodeStatus implements Model
{
    /**
     * @param NodeStatusMemory|null $memory Metrics about the memory usage.
     */
    public function __construct(
        public ?NodeStatusMemory $memory = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $memory = Data::mapOrNull($data, 'memory');

        return new self(
            memory: $memory === null ? null : NodeStatusMemory::fromArray($memory),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'memory' => $this->memory?->toArray(),
        ]);
    }
}
