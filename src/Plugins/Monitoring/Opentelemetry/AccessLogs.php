<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Opentelemetry;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.access_logs` object of the OpenTelemetry plugin (doc `Monitoring/opentelemetry.md`).
 */
#[PluginSchema('Monitoring/opentelemetry.md', '#/properties/config/properties/access_logs')]
final readonly class AccessLogs implements Model
{
    /**
     * @param array<array-key, string>|null $customAttributesByLua A key-value map that dynamically modifies access log fields using Lua code.
     * @param string|null                   $endpoint              An HTTP URL endpoint where access logs (e.g.
     */
    public function __construct(
        public ?array $customAttributesByLua = null,
        public ?string $endpoint = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            customAttributesByLua: Data::stringMapOrNull($data, 'custom_attributes_by_lua'),
            endpoint: Data::stringOrNull($data, 'endpoint'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'custom_attributes_by_lua' => $this->customAttributesByLua,
            'endpoint' => $this->endpoint,
        ]);
    }
}
