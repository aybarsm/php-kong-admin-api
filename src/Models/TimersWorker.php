<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `worker` object of Timers (spec `#/components/responses/GetTimersDebugInfoResponse/content/application~1json/schema.worker`).
 */
#[Schema('#/components/responses/GetTimersDebugInfoResponse/content/application~1json/schema/properties/worker')]
final readonly class TimersWorker implements Model
{
    /**
     * @param int|null $count The total number of Nginx worker processes.
     * @param int|null $id    The ordinal number of the current Nginx worker process (starting from 0).
     */
    public function __construct(
        public ?int $count = null,
        public ?int $id = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            count: Data::intOrNull($data, 'count'),
            id: Data::intOrNull($data, 'id'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'count' => $this->count,
            'id' => $this->id,
        ]);
    }
}
