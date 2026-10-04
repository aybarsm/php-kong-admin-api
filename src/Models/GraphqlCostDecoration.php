<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * A GraphQL cost decoration as returned by the Admin API (spec schema `GraphQLCostDecoration`).
 */
#[Schema('GraphQLCostDecoration')]
final readonly class GraphqlCostDecoration implements Model
{
    /**
     * @param string            $typePath
     * @param list<string>|null $addArguments
     * @param float|null        $addConstant
     * @param int|null          $createdAt    Unix epoch when the resource was created.
     * @param string|null       $id           A string representing a UUID (universally unique identifier).
     * @param list<string>|null $mulArguments
     * @param float|null        $mulConstant
     * @param ForeignKey|null   $service
     */
    public function __construct(
        public string $typePath,
        public ?array $addArguments = null,
        public ?float $addConstant = null,
        public ?int $createdAt = null,
        public ?string $id = null,
        public ?array $mulArguments = null,
        public ?float $mulConstant = null,
        public ?ForeignKey $service = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $service = Data::mapOrNull($data, 'service');

        return new self(
            typePath: Data::string($data, 'type_path'),
            addArguments: Data::stringListOrNull($data, 'add_arguments'),
            addConstant: Data::floatOrNull($data, 'add_constant'),
            createdAt: Data::intOrNull($data, 'created_at'),
            id: Data::stringOrNull($data, 'id'),
            mulArguments: Data::stringListOrNull($data, 'mul_arguments'),
            mulConstant: Data::floatOrNull($data, 'mul_constant'),
            service: $service === null ? null : ForeignKey::fromArray($service),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'type_path' => $this->typePath,
            'add_arguments' => $this->addArguments,
            'add_constant' => $this->addConstant,
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'mul_arguments' => $this->mulArguments,
            'mul_constant' => $this->mulConstant,
            'service' => $this->service?->toArray(),
        ]);
    }
}
