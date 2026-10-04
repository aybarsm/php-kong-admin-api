<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `plugins` object of KongInfo (spec `#/components/responses/GetKongInfoResponse/content/application~1json/schema.plugins`).
 */
#[Schema('#/components/responses/GetKongInfoResponse/content/application~1json/schema/properties/plugins')]
final readonly class KongInfoPlugins implements Model
{
    /**
     * @param array<array-key, mixed>|null $availableOnServer
     * @param list<string>|null            $enabledInCluster  A list of distinct plugin names enabled in the cluster.
     */
    public function __construct(
        public ?array $availableOnServer = null,
        public ?array $enabledInCluster = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            availableOnServer: Data::freeFormOrNull($data, 'available_on_server'),
            enabledInCluster: Data::stringListOrNull($data, 'enabled_in_cluster'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'available_on_server' => $this->availableOnServer,
            'enabled_in_cluster' => $this->enabledInCluster,
        ]);
    }
}
