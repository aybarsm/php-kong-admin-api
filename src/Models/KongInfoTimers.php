<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `timers` object of KongInfo (spec `#/components/responses/GetKongInfoResponse/content/application~1json/schema.timers`).
 */
#[Schema('#/components/responses/GetKongInfoResponse/content/application~1json/schema/properties/timers')]
final readonly class KongInfoTimers implements Model
{
    /**
     * @param int|null $pending The number of pending timers.
     * @param int|null $running The number of running timers.
     */
    public function __construct(
        public ?int $pending = null,
        public ?int $running = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            pending: Data::intOrNull($data, 'pending'),
            running: Data::intOrNull($data, 'running'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'pending' => $this->pending,
            'running' => $this->running,
        ]);
    }
}
