<?php

declare(strict_types=1);

namespace Aybarsm\Kong\AdminApi\Models;

use Aybarsm\Kong\AdminApi\Attributes\Schema;
use Aybarsm\Kong\AdminApi\Contracts\Model;
use Aybarsm\Kong\AdminApi\Internal\Data;
use Override;

/**
 * The `config.headers` object of Webhook (spec `#/components/requestBodies/AddWebhook/content/application~1json/schema.config.headers`).
 */
#[Schema('#/components/requestBodies/AddWebhook/content/application~1json/schema/properties/config.headers')]
final readonly class WebhookHeaders implements Model
{
    /**
     * @param string|null $headers Optional configuration header
     */
    public function __construct(
        public ?string $headers = null,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    #[Override]
    public static function fromArray(array $data): static
    {
        return new self(
            headers: Data::stringOrNull($data, 'headers'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function toArray(): array
    {
        return Data::withoutNulls([
            'headers' => $this->headers,
        ]);
    }
}
