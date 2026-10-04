<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * Request body for creating, updating or upserting a DeGraphQL route
 * (spec schema `Degraphql_route`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('Degraphql_route')]
final readonly class DegraphqlRouteInput implements Input
{
    /**
     * @param string|null            $query     Required by the spec on create.
     * @param ForeignKey|string|null $service   Required by the spec on create.
     * @param string|null            $uri       Required by the spec on create.
     * @param int|null               $createdAt Unix epoch when the resource was created.
     * @param string|null            $id        A string representing a UUID (universally unique identifier).
     * @param list<string>|null      $methods
     * @param int|null               $updatedAt Unix epoch when the resource was last updated.
     */
    public function __construct(
        public ?string $query = null,
        public ForeignKey|string|null $service = null,
        public ?string $uri = null,
        public ?int $createdAt = null,
        public ?string $id = null,
        public ?array $methods = null,
        public ?int $updatedAt = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'query' => $this->query,
            'service' => $this->service === null ? null : ForeignKey::of($this->service)->toArray(),
            'uri' => $this->uri,
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'methods' => $this->methods,
            'updated_at' => $this->updatedAt,
        ]);
    }
}
