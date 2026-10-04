<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models\Shared;

use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * Additional Subject Alternative Names matched on the upstream certificate (spec `Service.tls_sans`).
 */
final readonly class TlsSans implements Model
{
    /**
     * @param list<string>|null $dnsnames DNS names for TLS verification
     * @param list<string>|null $uris     URIs for TLS verification
     */
    public function __construct(
        public ?array $dnsnames = null,
        public ?array $uris = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            dnsnames: Data::stringListOrNull($data, 'dnsnames'),
            uris: Data::stringListOrNull($data, 'uris'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'dnsnames' => $this->dnsnames,
            'uris' => $this->uris,
        ]);
    }
}
