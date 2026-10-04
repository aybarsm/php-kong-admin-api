<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * A DeGraphQL route as returned by the Admin API (spec schema `Degraphql_route`).
 */
#[Schema('Degraphql_route')]
final readonly class DegraphqlRoute implements Model
{
    /**
     * @param string            $query
     * @param ForeignKey        $service
     * @param string            $uri
     * @param int|null          $createdAt Unix epoch when the resource was created.
     * @param string|null       $id        A string representing a UUID (universally unique identifier).
     * @param list<string>|null $methods
     * @param int|null          $updatedAt Unix epoch when the resource was last updated.
     */
    public function __construct(
        public string $query,
        public ForeignKey $service,
        public string $uri,
        public ?int $createdAt = null,
        public ?string $id = null,
        public ?array $methods = null,
        public ?int $updatedAt = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            query: Data::string($data, 'query'),
            service: ForeignKey::fromArray(Data::map($data, 'service')),
            uri: Data::string($data, 'uri'),
            createdAt: Data::intOrNull($data, 'created_at'),
            id: Data::stringOrNull($data, 'id'),
            methods: Data::stringListOrNull($data, 'methods'),
            updatedAt: Data::intOrNull($data, 'updated_at'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'query' => $this->query,
            'service' => $this->service->toArray(),
            'uri' => $this->uri,
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'methods' => $this->methods,
            'updated_at' => $this->updatedAt,
        ]);
    }
}
