<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\ProxyCache;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.memory` object of the Proxy Cache plugin (doc `TrafficControl/proxy-cache.md`).
 */
#[PluginSchema('TrafficControl/proxy-cache.md', '#/properties/config/properties/memory')]
final readonly class Memory implements Model
{
    /**
     * @param string|null $dictionaryName The name of the shared dictionary in which to hold cache entities when the memory strategy is selected. Default: `kong_db_cache`.
     */
    public function __construct(
        public ?string $dictionaryName = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            dictionaryName: Data::stringOrNull($data, 'dictionary_name'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'dictionary_name' => $this->dictionaryName,
        ]);
    }
}
