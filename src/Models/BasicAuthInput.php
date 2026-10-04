<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Input;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Override;
use SensitiveParameter;

/**
 * Request body for creating, updating or upserting a Basic-auth credential
 * (spec schema `BasicAuth`).
 *
 * Every field is optional because PATCH reuses the schema; null fields are not sent.
 * To send an explicit null, pass an array instead.
 */
#[Schema('BasicAuth')]
final readonly class BasicAuthInput implements Input
{
    /** Properties the spec marks `x-encrypted` (plus reviewed secrets, spec-notes Q18); redacted in __debugInfo(). */
    private const array ENCRYPTED = ['password'];

    /**
     * @param string|null            $password  Required by the spec on create.
     * @param string|null            $username  Required by the spec on create.
     * @param ForeignKey|string|null $consumer
     * @param int|null               $createdAt Unix epoch when the resource was created.
     * @param string|null            $id        A string representing a UUID (universally unique identifier).
     * @param list<string>|null      $tags      A set of strings representing tags.
     */
    public function __construct(
        #[SensitiveParameter]
        public ?string $password = null,
        public ?string $username = null,
        public ForeignKey|string|null $consumer = null,
        public ?int $createdAt = null,
        public ?string $id = null,
        public ?array $tags = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'password' => $this->password,
            'username' => $this->username,
            'consumer' => $this->consumer === null ? null : ForeignKey::of($this->consumer)->toArray(),
            'created_at' => $this->createdAt,
            'id' => $this->id,
            'tags' => $this->tags,
        ]);
    }

    /**
     * Redacts `x-encrypted` values.
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        $values = get_object_vars($this);
        foreach (self::ENCRYPTED as $property) {
            if ($values[$property] !== null) {
                $values[$property] = '***';
            }
        }

        return $values;
    }
}
