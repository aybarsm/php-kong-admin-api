<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * A Vault as returned by the Admin API (spec schema `Vault`).
 */
#[Schema('Vault')]
final readonly class Vault implements Model
{
    /**
     * @param string                       $name        The name of the Vault that's going to be added.
     * @param string                       $prefix      The unique prefix (or identifier) for this Vault configuration.
     * @param array<array-key, mixed>|null $config      The configuration properties for the Vault which can be found on the vaults' documentation page.
     * @param int|null                     $createdAt   Unix epoch when the resource was created.
     * @param string|null                  $description The description of the Vault entity.
     * @param string|null                  $id          A string representing a UUID (universally unique identifier).
     * @param list<string>|null            $tags        An optional set of strings associated with the Vault for grouping and filtering.
     * @param int|null                     $updatedAt   Unix epoch when the resource was last updated.
     */
    public function __construct(
        public string $name,
        public string $prefix,
        public ?array $config = null,
        public ?int $createdAt = null,
        public ?string $description = null,
        public ?string $id = null,
        public ?array $tags = null,
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
            name: Data::string($data, 'name'),
            prefix: Data::string($data, 'prefix'),
            config: Data::freeFormOrNull($data, 'config'),
            createdAt: Data::intOrNull($data, 'created_at'),
            description: Data::stringOrNull($data, 'description'),
            id: Data::stringOrNull($data, 'id'),
            tags: Data::stringListOrNull($data, 'tags'),
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
            'name' => $this->name,
            'prefix' => $this->prefix,
            'config' => $this->config,
            'created_at' => $this->createdAt,
            'description' => $this->description,
            'id' => $this->id,
            'tags' => $this->tags,
            'updated_at' => $this->updatedAt,
        ]);
    }
}
