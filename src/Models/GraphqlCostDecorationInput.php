<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * Request body for creating, updating or upserting a GraphQL cost decoration
 * (spec schema `GraphQLCostDecoration`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('GraphQLCostDecoration')]
final readonly class GraphqlCostDecorationInput implements Input
{
    /**
     * @param string|null            $typePath     Required by the spec on create.
     * @param list<string>|null      $addArguments
     * @param float|null             $addConstant
     * @param int|null               $createdAt    Unix epoch when the resource was created.
     * @param string|null            $id           A string representing a UUID (universally unique identifier).
     * @param list<string>|null      $mulArguments
     * @param float|null             $mulConstant
     * @param ForeignKey|string|null $service
     */
    public function __construct(
        public ?string $typePath = null,
        public ?array $addArguments = null,
        public ?float $addConstant = null,
        public ?int $createdAt = null,
        public ?string $id = null,
        public ?array $mulArguments = null,
        public ?float $mulConstant = null,
        public ForeignKey|string|null $service = null,
    ) {
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
            'service' => $this->service === null ? null : ForeignKey::of($this->service)->toArray(),
        ]);
    }
}
