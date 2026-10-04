<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * FIPS status from `GET /fips-status` (spec response `FIPS-response`).
 */
#[Schema('#/components/responses/FIPS-response/content/application~1json/schema')]
final readonly class FipsStatus implements Model
{
    /**
     * @param bool|null   $active  Indicates if FIPS mode is currently active (true) or inactive (false).
     * @param string|null $version The version of the FIPS module, or 'unknown' if the version cannot be determined.
     */
    public function __construct(
        public ?bool $active = null,
        public ?string $version = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            active: Data::boolOrNull($data, 'active'),
            version: Data::stringOrNull($data, 'version'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'active' => $this->active,
            'version' => $this->version,
        ]);
    }
}
