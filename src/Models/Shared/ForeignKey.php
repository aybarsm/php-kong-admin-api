<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models\Shared;

use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * A reference to another entity: the spec's `x-foreign` object `{id}`.
 */
final readonly class ForeignKey implements Model
{
    public function __construct(
        public string $id,
    ) {
    }

    /**
     * Wraps an entity ID, passing an existing reference through.
     */
    public static function of(self|string $reference): self
    {
        return $reference instanceof self ? $reference : new self($reference);
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(Data::string($data, 'id'));
    }

    /**
     * @return array{id: string}
     */
    #[Override]
    public function toArray(): array
    {
        return ['id' => $this->id];
    }
}
