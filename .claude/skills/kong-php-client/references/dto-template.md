# DTO templates

**References: `src/Models/Service.php` (output) and `src/Models/ServiceInput.php` (input).** Copy them.

## Output DTO (spec schema `Service`)
```php
<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Enums\Protocol;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Aybarsm\Kong\AdminApi\Models\Shared\ForeignKey;
use Aybarsm\Kong\AdminApi\Models\Shared\TlsSans;
use Override;

/**
 * A Kong Service as returned by the Admin API (spec schema `Service`).
 */
#[Schema('Service')]
final readonly class Service implements Model
{
    /**
     * @param list<string>|null $caCertificates
     * @param list<string>|null $tags
     */
    public function __construct(
        public string $host,                       // required in spec
        public ?string $id = null,
        public ?string $name = null,
        public ?Protocol $protocol = null,
        public ?int $port = null,
        public ?string $path = null,
        public ?int $retries = null,
        public ?int $connectTimeout = null,
        public ?int $writeTimeout = null,
        public ?int $readTimeout = null,
        public ?bool $enabled = null,
        public ?array $caCertificates = null,
        public ?ForeignKey $clientCertificate = null,   // x-foreign
        public ?TlsSans $tlsSans = null,
        public ?bool $tlsVerify = null,
        public ?int $tlsVerifyDepth = null,
        public ?array $tags = null,
        public ?int $createdAt = null,
        public ?int $updatedAt = null,
        // `url` is writeOnly in the spec → only on ServiceInput
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        $clientCertificate = Data::mapOrNull($data, 'client_certificate');
        $tlsSans = Data::mapOrNull($data, 'tls_sans');

        return new self(
            host: Data::string($data, 'host'),
            id: Data::stringOrNull($data, 'id'),
            name: Data::stringOrNull($data, 'name'),
            protocol: Data::enumOrNull($data, 'protocol', Protocol::class),
            port: Data::intOrNull($data, 'port'),
            path: Data::stringOrNull($data, 'path'),
            retries: Data::intOrNull($data, 'retries'),
            connectTimeout: Data::intOrNull($data, 'connect_timeout'),
            writeTimeout: Data::intOrNull($data, 'write_timeout'),
            readTimeout: Data::intOrNull($data, 'read_timeout'),
            enabled: Data::boolOrNull($data, 'enabled'),
            caCertificates: Data::stringListOrNull($data, 'ca_certificates'),
            clientCertificate: $clientCertificate === null ? null : ForeignKey::fromArray($clientCertificate),
            tlsSans: $tlsSans === null ? null : TlsSans::fromArray($tlsSans),
            tlsVerify: Data::boolOrNull($data, 'tls_verify'),
            tlsVerifyDepth: Data::intOrNull($data, 'tls_verify_depth'),
            tags: Data::stringListOrNull($data, 'tags'),
            createdAt: Data::intOrNull($data, 'created_at'),
            updatedAt: Data::intOrNull($data, 'updated_at'),
        );
    }

    /** @return array<string, mixed> */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'host' => $this->host,
            'id' => $this->id,
            'name' => $this->name,
            'protocol' => $this->protocol?->value,
            // … every property, snake_case key …
            'client_certificate' => $this->clientCertificate?->toArray(),
            'tls_sans' => $this->tlsSans?->toArray(),
        ]);
    }
}
```

## Input DTO (`ServiceInput`)
- `final readonly class ServiceInput implements Input`. **Every** parameter is `?T = null`, because the
  spec reuses the schema for PATCH. Spec-`required` fields are noted in the docblock (`@param string|null
  $host Required on create.`).
- It includes `writeOnly` fields (`url`) and excludes server-generated ones only if the spec marks them `readOnly`.
- Foreign keys are passed as `ForeignKey|string|null`; a string is wrapped to `{id: …}`.
- `toArray()` returns `array<string, mixed>` and omits nulls. Document that callers pass an array to
  send an explicit null.
- `x-encrypted` fields get `#[\SensitiveParameter]` and are redacted in `__debugInfo()`.

## Enum
```php
enum Protocol: string
{
    case Grpc = 'grpc';
    case Grpcs = 'grpcs';
    case Http = 'http';
    // … values exactly as the spec lists them; EnumConformanceTest compares them
}
```

## `Internal\Data` readers
`string`, `stringOrNull`, `int`, `intOrNull`, `floatOrNull` (spec `number`), `bool`, `boolOrNull`,
`stringListOrNull`, `intListOrNull`, `map`/`mapOrNull` (spec objects with `properties`),
`freeFormOrNull` (spec `type: object` without properties, typed `array<array-key, mixed>`),
`listOfMaps`/`listOfMapsOrNull`, `enumOrNull`/`enumListOrNull` (an unknown case throws), `withoutNulls`.
Each reader throws `UnexpectedResponseException` naming the key.

## Checklist
- [ ] `#[Schema('<exact spec schema name>')]`
- [ ] Property set == spec properties (ModelConformanceTest)
- [ ] `required` → non-nullable, everything else nullable with a null default
- [ ] `x-foreign` → `ForeignKey`, objects with properties → nested DTO, free-form → `array<string, mixed>`
- [ ] Every array has a PHPDoc shape. No native `mixed`.
- [ ] `#[Override]` on `fromArray`/`toArray`
