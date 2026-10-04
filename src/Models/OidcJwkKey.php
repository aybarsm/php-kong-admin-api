<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `jwks.keys[]` object of OidcJwk (spec `OidcJwk.jwks.keys[]`).
 */
#[Schema('#/components/schemas/OidcJwk/properties/jwks/properties/keys')]
final readonly class OidcJwkKey implements Model
{
    /** Properties the spec marks `x-encrypted`; redacted in __debugInfo(). */
    private const array ENCRYPTED = ['d', 'dp', 'dq', 'k', 'oth', 'p', 'q', 'qi', 'r', 't'];

    /**
     * @param string|null       $alg
     * @param string|null       $crv
     * @param string|null       $d
     * @param string|null       $dp
     * @param string|null       $dq
     * @param string|null       $e
     * @param string|null       $issuer
     * @param string|null       $k
     * @param list<string>|null $keyOps
     * @param string|null       $kid
     * @param string|null       $kty
     * @param string|null       $n
     * @param string|null       $oth
     * @param string|null       $p
     * @param string|null       $q
     * @param string|null       $qi
     * @param string|null       $r
     * @param string|null       $t
     * @param string|null       $use
     * @param string|null       $x
     * @param list<string>|null $x5c
     * @param string|null       $x5t
     * @param string|null       $x5tS256
     * @param string|null       $x5u
     * @param string|null       $y
     */
    public function __construct(
        public ?string $alg = null,
        public ?string $crv = null,
        public ?string $d = null,
        public ?string $dp = null,
        public ?string $dq = null,
        public ?string $e = null,
        public ?string $issuer = null,
        public ?string $k = null,
        public ?array $keyOps = null,
        public ?string $kid = null,
        public ?string $kty = null,
        public ?string $n = null,
        public ?string $oth = null,
        public ?string $p = null,
        public ?string $q = null,
        public ?string $qi = null,
        public ?string $r = null,
        public ?string $t = null,
        public ?string $use = null,
        public ?string $x = null,
        public ?array $x5c = null,
        public ?string $x5t = null,
        public ?string $x5tS256 = null,
        public ?string $x5u = null,
        public ?string $y = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            alg: Data::stringOrNull($data, 'alg'),
            crv: Data::stringOrNull($data, 'crv'),
            d: Data::stringOrNull($data, 'd'),
            dp: Data::stringOrNull($data, 'dp'),
            dq: Data::stringOrNull($data, 'dq'),
            e: Data::stringOrNull($data, 'e'),
            issuer: Data::stringOrNull($data, 'issuer'),
            k: Data::stringOrNull($data, 'k'),
            keyOps: Data::stringListOrNull($data, 'key_ops'),
            kid: Data::stringOrNull($data, 'kid'),
            kty: Data::stringOrNull($data, 'kty'),
            n: Data::stringOrNull($data, 'n'),
            oth: Data::stringOrNull($data, 'oth'),
            p: Data::stringOrNull($data, 'p'),
            q: Data::stringOrNull($data, 'q'),
            qi: Data::stringOrNull($data, 'qi'),
            r: Data::stringOrNull($data, 'r'),
            t: Data::stringOrNull($data, 't'),
            use: Data::stringOrNull($data, 'use'),
            x: Data::stringOrNull($data, 'x'),
            x5c: Data::stringListOrNull($data, 'x5c'),
            x5t: Data::stringOrNull($data, 'x5t'),
            x5tS256: Data::stringOrNull($data, 'x5t#S256'),
            x5u: Data::stringOrNull($data, 'x5u'),
            y: Data::stringOrNull($data, 'y'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'alg' => $this->alg,
            'crv' => $this->crv,
            'd' => $this->d,
            'dp' => $this->dp,
            'dq' => $this->dq,
            'e' => $this->e,
            'issuer' => $this->issuer,
            'k' => $this->k,
            'key_ops' => $this->keyOps,
            'kid' => $this->kid,
            'kty' => $this->kty,
            'n' => $this->n,
            'oth' => $this->oth,
            'p' => $this->p,
            'q' => $this->q,
            'qi' => $this->qi,
            'r' => $this->r,
            't' => $this->t,
            'use' => $this->use,
            'x' => $this->x,
            'x5c' => $this->x5c,
            'x5t' => $this->x5t,
            'x5t#S256' => $this->x5tS256,
            'x5u' => $this->x5u,
            'y' => $this->y,
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
