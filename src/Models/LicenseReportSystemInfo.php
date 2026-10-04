<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `system_info` object of LicenseReport (spec `#/components/responses/ReportResponse/content/application~1json/schema.system_info`).
 */
#[Schema('#/components/responses/ReportResponse/content/application~1json/schema/properties/system_info')]
final readonly class LicenseReportSystemInfo implements Model
{
    /**
     * @param int|null    $cores    The number of CPU cores available to the node.
     * @param string|null $hostname The hostname of the node.
     * @param string|null $uname    The operating system and architecture of the node.
     */
    public function __construct(
        public ?int $cores = null,
        public ?string $hostname = null,
        public ?string $uname = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            cores: Data::intOrNull($data, 'cores'),
            hostname: Data::stringOrNull($data, 'hostname'),
            uname: Data::stringOrNull($data, 'uname'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'cores' => $this->cores,
            'hostname' => $this->hostname,
            'uname' => $this->uname,
        ]);
    }
}
