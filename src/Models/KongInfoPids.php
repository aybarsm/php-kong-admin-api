<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `pids` object of KongInfo (spec `#/components/responses/GetKongInfoResponse/content/application~1json/schema.pids`).
 */
#[Schema('#/components/responses/GetKongInfoResponse/content/application~1json/schema/properties/pids')]
final readonly class KongInfoPids implements Model
{
    /**
     * @param int|null       $master  The PID of the master process.
     * @param list<int>|null $workers An array of worker process PIDs.
     */
    public function __construct(
        public ?int $master = null,
        public ?array $workers = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            master: Data::intOrNull($data, 'master'),
            workers: Data::intListOrNull($data, 'workers'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'master' => $this->master,
            'workers' => $this->workers,
        ]);
    }
}
