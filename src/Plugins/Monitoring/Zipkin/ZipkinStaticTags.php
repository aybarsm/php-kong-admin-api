<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\Monitoring\Zipkin;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.static_tags[]` object of the Zipkin plugin (doc `Monitoring/zipkin.md`).
 */
#[PluginSchema('Monitoring/zipkin.md', '#/properties/config/properties/static_tags/items')]
final readonly class ZipkinStaticTags implements Model
{
    /**
     * @param string $name
     * @param string $value
     */
    public function __construct(
        public string $name,
        public string $value,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            name: Data::string($data, 'name'),
            value: Data::string($data, 'value'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'name' => $this->name,
            'value' => $this->value,
        ]);
    }
}
