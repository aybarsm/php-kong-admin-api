<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The schema of a Partial type from `GET /schemas/partials/{partialType}` (spec response `GetPartialSchemaResponse`).
 */
#[Schema('#/components/responses/GetPartialSchemaResponse/content/application~1json/schema')]
final readonly class PartialTypeSchema implements Model
{
    /**
     * @param list<array<string, mixed>>|null $fields
     */
    public function __construct(
        public ?array $fields = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            fields: Data::listOfMapsOrNull($data, 'fields'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'fields' => $this->fields,
        ]);
    }
}
