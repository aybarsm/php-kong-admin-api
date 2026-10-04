<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;

/**
 * The response of `POST /keyring/import` (spec response `CreateKeyringImportResponse`).
 */
#[Schema('#/components/responses/CreateKeyringImportResponse/content/application~1json/schema')]
final readonly class KeyringImportResult implements Model
{
    /**
     * @param ForeignKey|null $consumer  The consumer object.
     * @param int|null        $createdAt Datetime representation of the keyring creation date.
     * @param string|null     $id        UUID of the keyring
     * @param string|null     $password  Password associated with the keyring.
     * @param string|null     $username  Username associated with the keyring
     */
    public function __construct(
        public ?ForeignKey $consumer = null,
        public ?int $createdAt = null,
        public ?string $id = null,
        public ?string $password = null,
        public ?string $username = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $consumer = Data::mapOrNull($data, 'consumer');

        return new self(
            consumer: $consumer === null ? null : ForeignKey::fromArray($consumer),
            createdAt: Data::intOrNull($data, 'created_at'),
            id: Data::stringOrNull($data, 'id'),
            password: Data::stringOrNull($data, 'password'),
            username: Data::stringOrNull($data, 'username'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'consumer' => $this->consumer?->toArray(),
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'password' => $this->password,
            'username' => $this->username,
        ]);
    }
}
