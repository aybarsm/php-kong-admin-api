<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The event-hook sources of `GET /event-hooks/sources` (spec response `ListSourcesResponse`).
 *
 * `data` is kept as a plain array: the spec describes this dynamic map by example (spec-notes Q17).
 */
#[Schema('#/components/responses/ListSourcesResponse/content/application~1json/schema')]
final readonly class EventHookSources implements Model
{
    /**
     * @param array<array-key, mixed>|null $data
     */
    public function __construct(
        public ?array $data = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            data: Data::freeFormOrNull($data, 'data'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'data' => $this->data,
        ]);
    }
}
