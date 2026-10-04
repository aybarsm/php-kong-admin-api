<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `license` object of LicenseReport (spec `#/components/responses/ReportResponse/content/application~1json/schema.license`).
 */
#[Schema('#/components/responses/ReportResponse/content/application~1json/schema/properties/license')]
final readonly class LicenseReportLicense implements Model
{
    /** Properties the spec marks `x-encrypted` (plus reviewed secrets, spec-notes Q18); redacted in __debugInfo(). */
    private const array ENCRYPTED = ['licenseKey'];

    /**
     * @param string|null $licenseExpirationDate The date on which the license expires.
     * @param string|null $licenseKey            The unique key identifying this license.
     */
    public function __construct(
        public ?string $licenseExpirationDate = null,
        public ?string $licenseKey = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            licenseExpirationDate: Data::stringOrNull($data, 'license_expiration_date'),
            licenseKey: Data::stringOrNull($data, 'license_key'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'license_expiration_date' => $this->licenseExpirationDate,
            'license_key' => $this->licenseKey,
        ]);
    }

    /**
     * Redacts `x-encrypted` values.
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        $values = get_object_vars($this);
        foreach (self::ENCRYPTED as $property) {
            if ($values[$property] !== null) {
                $values[$property] = '***';
            }
        }

        return $values;
    }
}
