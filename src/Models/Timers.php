<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Timer debug information from `GET /timers` (spec response `GetTimersDebugInfoResponse`).
 */
#[Schema('#/components/responses/GetTimersDebugInfoResponse/content/application~1json/schema')]
final readonly class Timers implements Model
{
    /**
     * @param TimersStats|null  $stats  Statistics about the worker.
     * @param TimersWorker|null $worker Information about the current worker.
     */
    public function __construct(
        public ?TimersStats $stats = null,
        public ?TimersWorker $worker = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $stats = Data::mapOrNull($data, 'stats');
        $worker = Data::mapOrNull($data, 'worker');

        return new self(
            stats: $stats === null ? null : TimersStats::fromArray($stats),
            worker: $worker === null ? null : TimersWorker::fromArray($worker),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'stats' => $this->stats?->toArray(),
            'worker' => $this->worker?->toArray(),
        ]);
    }
}
