<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * One entity/tag pair listed by `GET /tags` and `GET /tags/{tag}` (spec response `TagsResponse`).
 */
#[Schema('#/components/responses/TagsResponse/content/application~1json/schema/properties/data')]
final readonly class TagEntry implements Model
{
    /**
     * @param string|null $entityId
     * @param string|null $entityName
     * @param string|null $entityType
     * @param string|null $tag
     */
    public function __construct(
        public ?string $entityId = null,
        public ?string $entityName = null,
        public ?string $entityType = null,
        public ?string $tag = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            entityId: Data::stringOrNull($data, 'entity_id'),
            entityName: Data::stringOrNull($data, 'entity_name'),
            entityType: Data::stringOrNull($data, 'entity_type'),
            tag: Data::stringOrNull($data, 'tag'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'entity_id' => $this->entityId,
            'entity_name' => $this->entityName,
            'entity_type' => $this->entityType,
            'tag' => $this->tag,
        ]);
    }
}
