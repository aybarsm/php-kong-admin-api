<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The declarative configuration from `GET /config` (spec response `GetDeclarativeConfigResponse`).
 */
#[Schema('#/components/responses/GetDeclarativeConfigResponse/content/application~1json/schema')]
final readonly class DeclarativeConfig implements Model
{
    /**
     * @param string|null $config
     */
    public function __construct(
        public ?string $config = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            config: Data::stringOrNull($data, 'config'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'config' => $this->config,
        ]);
    }
}
