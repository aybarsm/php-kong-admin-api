<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * A Plugin linked to a Partial (spec schema `PartialLink`).
 */
#[Schema('PartialLink')]
final readonly class PartialLink implements Model
{
    /**
     * @param string      $id           The plugin's unique identifier
     * @param string      $name         The plugin's name
     * @param string|null $instanceName The instance name of the plugin
     */
    public function __construct(
        public string $id,
        public string $name,
        public ?string $instanceName = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            id: Data::string($data, 'id'),
            name: Data::string($data, 'name'),
            instanceName: Data::stringOrNull($data, 'instance_name'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'id' => $this->id,
            'name' => $this->name,
            'instance_name' => $this->instanceName,
        ]);
    }
}
