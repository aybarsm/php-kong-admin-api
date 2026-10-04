<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Plugins\TrafficControl\ProxyCache;

use Aybarsm\Kong\AdminApi\Attributes\PluginSchema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.response_headers` object of the Proxy Cache plugin (doc `TrafficControl/proxy-cache.md`).
 */
#[PluginSchema('TrafficControl/proxy-cache.md', '#/properties/config/properties/response_headers')]
final readonly class ResponseHeaders implements Model
{
    /**
     * @param bool|null $XCacheKey    Default: `true`.
     * @param bool|null $XCacheStatus Default: `true`.
     * @param bool|null $age          Default: `true`.
     */
    public function __construct(
        public ?bool $XCacheKey = null,
        public ?bool $XCacheStatus = null,
        public ?bool $age = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            XCacheKey: Data::boolOrNull($data, 'X-Cache-Key'),
            XCacheStatus: Data::boolOrNull($data, 'X-Cache-Status'),
            age: Data::boolOrNull($data, 'age'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'X-Cache-Key' => $this->XCacheKey,
            'X-Cache-Status' => $this->XCacheStatus,
            'age' => $this->age,
        ]);
    }
}
