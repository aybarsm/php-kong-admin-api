<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Request body for creating, updating or upserting a Vault
 * (spec schema `Vault`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('Vault')]
final readonly class VaultInput implements Input
{
    /**
     * @param string|null                  $name        The name of the Vault that's going to be added. Required by the spec on create.
     * @param string|null                  $prefix      The unique prefix (or identifier) for this Vault configuration. Required by the spec on create.
     * @param array<array-key, mixed>|null $config      The configuration properties for the Vault which can be found on the vaults' documentation page.
     * @param int|null                     $createdAt   Unix epoch when the resource was created.
     * @param string|null                  $description The description of the Vault entity.
     * @param string|null                  $id          A string representing a UUID (universally unique identifier).
     * @param list<string>|null            $tags        An optional set of strings associated with the Vault for grouping and filtering.
     * @param int|null                     $updatedAt   Unix epoch when the resource was last updated.
     */
    public function __construct(
        public ?string $name = null,
        public ?string $prefix = null,
        public ?array $config = null,
        public ?int $createdAt = null,
        public ?string $description = null,
        public ?string $id = null,
        public ?array $tags = null,
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
