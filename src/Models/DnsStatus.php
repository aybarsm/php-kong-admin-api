<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * DNS status from `GET /status/dns` (spec response `GetDNSStatusResponse`).
 */
#[Schema('#/components/responses/GetDNSStatusResponse/content/application~1json/schema')]
final readonly class DnsStatus implements Model
{
    /**
     * @param DnsStatusWorker|null $worker Worker details.
     */
    public function __construct(
        public ?DnsStatusWorker $worker = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $worker = Data::mapOrNull($data, 'worker');

        return new self(
            worker: $worker === null ? null : DnsStatusWorker::fromArray($worker),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'worker' => $this->worker?->toArray(),
        ]);
    }
}
