<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The result of `POST /schemas/{entityName}/validate` (spec response `ValidateEntityResponse`).
 */
#[Schema('#/components/responses/ValidateEntityResponse/content/application~1json/schema')]
final readonly class SchemaValidation implements Model
{
    /**
     * @param string|null $message
     */
    public function __construct(
        public ?string $message = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            message: Data::stringOrNull($data, 'message'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'message' => $this->message,
        ]);
    }
}
